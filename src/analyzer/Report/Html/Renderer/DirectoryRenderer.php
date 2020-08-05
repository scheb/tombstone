<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerDirectoryResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateProvider;
use Scheb\Tombstone\Core\PathNormalizer;
use SebastianBergmann\Template\Template;

class DirectoryRenderer
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
     * @var DirectoryItemRenderer
     */
    private $directoryItemRenderer;

    /**
     * @var Template|\Text_Template
     */
    private $directoryTemplate;

    public function __construct(string $reportDir, BreadCrumbRenderer $breadCrumbRenderer, DirectoryItemRenderer $directoryItemRenderer)
    {
        $this->reportDir = $reportDir;
        $this->breadCrumbRenderer = $breadCrumbRenderer;
        $this->directoryItemRenderer = $directoryItemRenderer;
        $this->directoryTemplate = TemplateProvider::getTemplate('directory.html');
    }

    public function generate(AnalyzerResult $result): void
    {
        $this->renderDirectoryRecursively($result->getRootDirectoryResult());
    }

    private function renderDirectoryRecursively(AnalyzerDirectoryResult $directoryResult): void
    {
        $this->renderDirectory($directoryResult);
        foreach ($directoryResult->getSubDirectoryResults() as $subDirectoryResult) {
            $this->renderDirectoryRecursively($subDirectoryResult);
        }
    }

    private function renderDirectory(AnalyzerDirectoryResult $directory): void
    {
        $directoryPath = $directory->getDirectoryPath();
        $directoryDepth = '' !== $directoryPath ? substr_count($directoryPath, PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR) + 1 : 0;
        $pathToRoot = str_repeat('../', $directoryDepth);
        $directoryListing = '';

        foreach ($directory->getSubDirectoryResults() as $subDirectoryResult) {
            $subDirectoryName = $subDirectoryResult->getDirectoryName();
            $itemLink = htmlspecialchars($subDirectoryName).'/index.html';
            if ($subDirectoryResult->getDeadCount() || $subDirectoryResult->getUndeadCount()) {
                $directoryListing .= $this->directoryItemRenderer->renderDirectoryItem($subDirectoryName, $itemLink, $subDirectoryResult, $pathToRoot);
            }
        }

        foreach ($directory->getFileResults() as $fileResult) {
            $fileName = basename($fileResult->getFile()->getReferencePath());
            $itemLink = htmlspecialchars($fileName).'.html';
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $directoryListing .= $this->directoryItemRenderer->renderDirectoryItem($fileName, $itemLink, $fileResult, $pathToRoot);
            }
        }

        $this->directoryTemplate->setVar([
            'path_to_root' => $pathToRoot,
            'item_local_path' => htmlspecialchars(PathNormalizer::normalizeDirectorySeparatorForEnvironment($directoryPath)),
            'date' => date('r'),
            'breadcrumb' => $this->breadCrumbRenderer->renderBreadcrumbToDirectory($directoryPath),
            'files_list' => $directoryListing,
        ]);

        $reportFileDirectory = FileSystem::createPath($this->reportDir, $directoryPath);
        FileSystem::ensureDirectoryCreated($reportFileDirectory);
        $reportFile = FileSystem::createPath($reportFileDirectory, 'index.html');
        $this->directoryTemplate->renderTo($reportFile);
    }
}
