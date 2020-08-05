<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Tombstone;
use SebastianBergmann\Template\Template;

class FileRenderer
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var RootPath
     */
    private $sourceRootPath;

    /**
     * @var Template|\Text_Template
     */
    private $fileTemplate;

    /**
     * @var Template|\Text_Template
     */
    private $tombstoneTemplate;

    public function __construct(string $reportDir, RootPath $sourceRootPath)
    {
        $this->reportDir = $reportDir;
        $this->sourceRootPath = $sourceRootPath;
        $this->fileTemplate = TemplateProvider::getTemplate('file.html');
        $this->tombstoneTemplate = TemplateProvider::getTemplate('file_tombstone.html');
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

        $tombstonesList = $this->renderTombstonesList($fileResult);
        $sourceCode = $this->formatSourceCode($fileResult);
        $relativeFilePath = $filePath->getRelativePath();
        $this->fileTemplate->setVar([
            'path_to_root' => './'.str_repeat('../', substr_count($relativeFilePath, '/')),
            'full_path' => htmlspecialchars($fileResult->getFile()->getAbsolutePath()),
            'breadcrumb' => $this->renderBreadcrumb($relativeFilePath),
            'tombstones_list' => $tombstonesList,
            'source_code' => $sourceCode,
            'date' => date('r'),
        ]);

        $reportFile = $this->reportDir.'/'.$relativeFilePath.'.html';
        $reportDir = \dirname($reportFile);
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
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

    private function formatSourceCode(AnalyzerFileResult $fileResult): string
    {
        $deadLines = [];
        $undeadLines = [];
        foreach ($fileResult->getDead() as $tombstone) {
            $deadLines[] = $tombstone->getLine();
        }
        foreach ($fileResult->getUndead() as $tombstone) {
            $undeadLines[] = $tombstone->getLine();
        }

        $formattedCode = '';
        $i = 0;
        $code = PhpFileFormatter::loadFile($fileResult->getFile()->getAbsolutePath());
        $lineTemplate = '<tr class="%s"><td class="number"><div align="right"><a name="%d"></a><a href="#%d">%d</a></div></td><td class="codeLine">%s</td></tr>';
        foreach ($code as $codeLine) {
            ++$i;

            $class = 'default';
            if (\in_array($i, $undeadLines)) {
                $class = 'danger icon-vampire';
            } elseif (\in_array($i, $deadLines)) {
                $class = 'success icon-cross';
            }

            $formattedCode .= sprintf($lineTemplate, $class, $i, $i, $i, $codeLine);
        }

        return $formattedCode;
    }

    private function renderBreadcrumb(string $relativeFilePath): string
    {
        $parts = explode('/', $relativeFilePath);
        $numParts = \count($parts);
        $rootDirName = htmlspecialchars(substr($this->sourceRootPath->getAbsolutePath(), 0, -1));
        $breadcrumbString = '<li class="breadcrumb-item"><a href="'.str_repeat('../', $numParts - 1).'index.html">'.$rootDirName.'</a></li> ';

        $folderUp = $numParts - 2;
        while ($label = array_shift($parts)) {
            if (!$parts) {
                $breadcrumbString .= '<li class="breadcrumb-item active">'.$label.'</li> ';
            } else {
                $link = str_repeat('../', $folderUp).'index.html';
                $breadcrumbString .= sprintf('<li class="breadcrumb-item"><a href="%s">%s</a></li> ', $link, htmlspecialchars($label));
            }
            --$folderUp;
        }

        return $breadcrumbString;
    }
}
