<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\Model\AnalyzerDirectoryResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Model\ResultAggregateInterface;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateFactory;
use Scheb\Tombstone\Core\Model\RootPath;

class DirectoryRenderer
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
     * @var \Text_Template
     */
    private $directoryTemplate;

    /**
     * @var \Text_Template
     */
    private $directoryItemTemplate;

    /**
     * @var \Text_Template
     */
    private $barTemplate;

    public function __construct(string $reportDir, RootPath $sourceRootPath)
    {
        $this->reportDir = $reportDir;
        $this->sourceRootPath = $sourceRootPath;
        $this->directoryTemplate = TemplateFactory::getTemplate('directory.html');
        $this->directoryItemTemplate = TemplateFactory::getTemplate('directory_item.html');
        $this->barTemplate = TemplateFactory::getTemplate('percentage_bar.html');
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
        $pathToRoot = str_repeat('../', substr_count($directoryPath, '/') + ($directoryPath ? 1 : 0));

        $directoryListing = '';
        foreach ($directory->getSubDirectoryResults() as $subDirectoryResult) {
            $subDirectoryName = $subDirectoryResult->getDirectoryName();
            $link = htmlspecialchars($subDirectoryName).'/index.html';
            if ($subDirectoryResult->getDeadCount() || $subDirectoryResult->getUndeadCount()) {
                $directoryListing .= $this->renderDirectoryItem($subDirectoryName, $link, $subDirectoryResult, $pathToRoot);
            }
        }
        foreach ($directory->getFileResults() as $fileResult) {
            $fileName = basename($fileResult->getFile()->getReferencePath());
            $link = htmlspecialchars($fileName).'.html';
            if ($fileResult->getDeadCount() || $fileResult->getUndeadCount()) {
                $directoryListing .= $this->renderDirectoryItem($fileName, $link, $fileResult, $pathToRoot);
            }
        }

        $this->directoryTemplate->setVar([
            'path_to_root' => $pathToRoot,
            'full_path' => htmlspecialchars($directoryPath),
            'breadcrumb' => $this->renderBreadcrumb($directoryPath),
            'files_list' => $directoryListing,
            'date' => date('r'),
        ]);

        $reportFile = $this->reportDir.'/'.htmlspecialchars($directoryPath).'/index.html';
        $reportDir = \dirname($reportFile);
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
        $this->directoryTemplate->renderTo($reportFile);
    }

    private function renderDirectoryItem(string $name, string $link, ResultAggregateInterface $result, string $pathToRoot): string
    {
        $deadCount = $result->getDeadCount();
        $undeadCount = $result->getUndeadCount();
        $totalCount = $deadCount + $undeadCount;

        $class = 'success';
        if ($undeadCount) {
            if ($undeadCount < $totalCount) {
                $class = 'warning';
            } else {
                $class = 'danger';
            }
        }

        $bar = $this->renderBar($deadCount, $totalCount);

        $this->directoryItemTemplate->setVar([
            'name' => htmlspecialchars($name),
            'path_to_root' => $pathToRoot,
            'icon' => $result instanceof AnalyzerFileResult ? 'code' : 'directory',
            'link' => $link,
            'class' => $class,
            'bar' => $bar,
            'total' => $totalCount,
            'numDead' => $deadCount,
            'numUndead' => $undeadCount,
        ]);

        return $this->directoryItemTemplate->render();
    }

    private function renderBar(int $numDead, int $total): string
    {
        $this->barTemplate->setVar([
            'level' => 'success',
            'percent' => round($numDead / $total * 100, 2),
        ]);

        return $this->barTemplate->render();
    }

    private function renderBreadcrumb(string $directoryPath): string
    {
        $rootDirName = htmlspecialchars(substr($this->sourceRootPath->getAbsolutePath(), 0, -1));

        if (!$directoryPath) {
            return '<li class="breadcrumb-item">'.$rootDirName.'</li> ';
        }

        $parts = explode('/', $directoryPath);
        $numParts = \count($parts);
        $breadcrumbString = '<li class="breadcrumb-item"><a href="'.str_repeat('../', $numParts).'index.html">'.htmlspecialchars($rootDirName).'</a></li> ';

        $folderUp = $numParts - 1;
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
