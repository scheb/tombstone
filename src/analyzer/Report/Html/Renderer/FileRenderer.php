<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\PathNormalizer;
use SebastianBergmann\Template\Template;

class FileRenderer
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var BreadCrumbRenderer
     */
    private $breadCrumbRenderer;

    /**
     * @var Template|\Text_Template
     */
    private $fileTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $tombstoneTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $sourceCodeTemplate;

    public function __construct(string $reportDir, BreadCrumbRenderer $breadCrumbRenderer)
    {
        $this->reportDir = $reportDir;
        $this->breadCrumbRenderer = $breadCrumbRenderer;
        $this->fileTemplate = TemplateProvider::getTemplate('file.html');
        $this->tombstoneTemplate = TemplateProvider::getTemplate('file_tombstone.html');
        $this->sourceCodeTemplate = TemplateProvider::getTemplate('file_source_code.html');
    }

    public function generate(AnalyzerResult $result): void
    {
        foreach ($result->getFileResults() as $file => $fileResult) {
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $this->renderFile($fileResult);
            }
        }
    }

    private function renderFile(AnalyzerFileResult $fileResult): void
    {
        $filePath = $fileResult->getFile();
        if (!($filePath instanceof RelativeFilePath)) {
            return;
        }

        $relativeFilePath = $filePath->getRelativePath();
        $this->fileTemplate->setVar([
            'path_to_root' => str_repeat('../', substr_count($relativeFilePath, PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR)),
            'item_local_path' => htmlspecialchars(PathNormalizer::normalizeDirectorySeparatorForEnvironment($relativeFilePath)),
            'date' => date('r'),
            'breadcrumb' => $this->breadCrumbRenderer->renderBreadcrumbToFile($relativeFilePath),
            'tombstones_list' => $this->renderTombstonesList($fileResult),
            'source_code' => $this->renderSourceCode($fileResult),
        ]);

        $reportFile = FileSystem::createPath($this->reportDir, $relativeFilePath.'.html');
        $reportFileDirectory = \dirname($reportFile);
        FileSystem::ensureDirectoryCreated($reportFileDirectory);
        $this->fileTemplate->renderTo($reportFile);
    }

    private function renderTombstonesList(AnalyzerFileResult $fileResult): string
    {
        $tombstoneList = [];

        /** @var Tombstone[] $renderTombstones */
        $renderTombstones = array_merge($fileResult->getDead(), $fileResult->getUndead());
        foreach ($renderTombstones as $tombstone) {
            if (!isset($tombstoneList[$tombstone->getLine()])) {
                $tombstoneList[$tombstone->getLine()] = '';
            }
            $tombstoneList[$tombstone->getLine()] .= $this->renderTombstoneItem(
                $tombstone,
                $tombstone->hasVampires() ? 'danger' : 'success'
            );
        }
        ksort($tombstoneList);

        return implode($tombstoneList);
    }

    private function renderTombstoneItem(Tombstone $tombstone, string $class): string
    {
        $this->tombstoneTemplate->setVar([
            'tombstone' => htmlspecialchars((string) $tombstone),
            'line' => $tombstone->getLine(),
            'method' => htmlspecialchars($tombstone->getMethod() ?? ''),
            'level' => $class,
        ]);

        return $this->tombstoneTemplate->render();
    }

    private function renderSourceCode(AnalyzerFileResult $fileResult): string
    {
        $deadLines = [];
        $undeadLines = [];
        foreach ($fileResult->getDead() as $tombstone) {
            $deadLines[$tombstone->getLine()] = true;
        }
        foreach ($fileResult->getUndead() as $tombstone) {
            $undeadLines[$tombstone->getLine()] = true;
        }

        $formattedCode = '';
        $lineNumber = 0;
        $code = PhpFileFormatter::loadFile($fileResult->getFile()->getAbsolutePath());
        foreach ($code as $codeLine) {
            ++$lineNumber;

            $class = 'default';
            if (isset($undeadLines[$lineNumber])) {
                $class = 'danger icon-vampire';
            } elseif (isset($deadLines[$lineNumber])) {
                $class = 'success icon-cross';
            }

            $formattedCode .= $this->renderCodeLine($class, $lineNumber, $codeLine);
        }

        return $formattedCode;
    }

    private function renderCodeLine(string $class, int $lineNumber, string $codeLine): string
    {
        $this->sourceCodeTemplate->setVar([
            'class' => $class,
            'line' => $lineNumber,
            'code' => $codeLine,
        ]);

        return $this->sourceCodeTemplate->render();
    }
}
