<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
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
     * @var FileTombstoneListRenderer
     */
    private $tombstoneListRenderer;

    /**
     * @var FileSourceCodeRenderer
     */
    private $sourceCodeRenderer;

    /**
     * @var Template|\Text_Template
     */
    private $fileTemplate;

    public function __construct(string $reportDir, BreadCrumbRenderer $breadCrumbRenderer, FileTombstoneListRenderer $tombstoneListRenderer, FileSourceCodeRenderer $sourceCodeRenderer)
    {
        $this->reportDir = $reportDir;
        $this->breadCrumbRenderer = $breadCrumbRenderer;
        $this->tombstoneListRenderer = $tombstoneListRenderer;
        $this->sourceCodeRenderer = $sourceCodeRenderer;
        $this->fileTemplate = TemplateProvider::getTemplate('file.html');
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
            'tombstones_list' => $this->tombstoneListRenderer->renderTombstonesList($fileResult),
            'source_code' => $this->sourceCodeRenderer->renderSourceCode($fileResult),
        ]);

        $reportFile = FileSystem::createPath($this->reportDir, $relativeFilePath.'.html');
        $reportFileDirectory = \dirname($reportFile);
        FileSystem::ensureDirectoryCreated($reportFileDirectory);
        $this->fileTemplate->renderTo($reportFile);
    }
}
