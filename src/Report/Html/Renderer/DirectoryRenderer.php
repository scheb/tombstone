<?php

namespace Scheb\Tombstone\Analyzer\Report\Html\Renderer;

use Scheb\Tombstone\Analyzer\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Cli\Application;
use Scheb\Tombstone\Analyzer\Report\Html\TemplateFactory;
use Scheb\Tombstone\Analyzer\Report\PathTools;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Tracing\PathNormalizer;

class DirectoryRenderer implements ReportGeneratorInterface
{
    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var string
     */
    private $sourceDir;

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

    public function __construct(string $reportDir, string $sourceDir)
    {
        $this->reportDir = $reportDir;
        $this->sourceDir = $sourceDir;
        $this->directoryTemplate = TemplateFactory::getTemplate('directory.html');
        $this->directoryItemTemplate = TemplateFactory::getTemplate('directory_item.html');
        $this->barTemplate = TemplateFactory::getTemplate('percentage_bar.html');
    }

    public function generate(AnalyzerResult $result): void
    {
        $tree = new ResultDirectory();
        $files = $result->getPerFile();
        foreach ($files as $fileResult) {
            $relativePath = PathNormalizer::makeRelativeTo($fileResult->getFile(), $this->sourceDir);
            $tree->addFileResult($relativePath, $fileResult);
        }

        $this->renderDirectoryRecursively($tree);
    }

    private function renderDirectoryRecursively(ResultDirectory $directory): void
    {
        $this->renderDirectory($directory);
        foreach ($directory->getDirectories() as $subDir) {
            $this->renderDirectoryRecursively($subDir);
        }
    }

    private function renderDirectory(ResultDirectory $directory): void
    {
        $directoryPath = $directory->getPath();
        $pathToRoot = './'.str_repeat('../', substr_count($directoryPath, '/') + ($directoryPath ? 1 : 0));
        $filesList = '';
        foreach ($directory->getDirectories() as $subDir) {
            $name = $subDir->getName();
            $link = './'.$subDir->getName().'/index.html';
            $filesList .= $this->renderDirectoryItem($name, $link, $subDir, $pathToRoot);
        }
        foreach ($directory->getFiles() as $fileResult) {
            $name = basename($fileResult->getFile());
            $link = './'.$name.'.html';
            $filesList .= $this->renderDirectoryItem($name, $link, $fileResult, $pathToRoot);
        }

        $this->directoryTemplate->setVar([
            'path_to_root' => $pathToRoot,
            'full_path' => PathTools::makePathAbsolute($directoryPath, $this->sourceDir),
            'breadcrumb' => $this->renderBreadcrumb($directoryPath),
            'files_list' => $filesList,
            'date' => date('r'),
            'version' => Application::VERSION,
        ]);

        $reportFile = $this->reportDir.DIRECTORY_SEPARATOR.$directoryPath.'/index.html';
        $reportDir = dirname($reportFile);
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0777, true);
        }
        $this->directoryTemplate->renderTo($reportFile);
    }

    /**
     * @param string                             $name
     * @param string                             $link
     * @param AnalyzerFileResult|ResultDirectory $result
     * @param string                             $pathToRoot
     *
     * @return string
     */
    private function renderDirectoryItem(string $name, string $link, $result, string $pathToRoot): string
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
            'name' => $name,
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
        if (!$directoryPath) {
            return '<li class="breadcrumb-item">'.$this->sourceDir.'</li> ';
        }

        $parts = explode('/', $directoryPath);
        $numParts = count($parts);
        $breadcrumbString = '<li class="breadcrumb-item"><a href="./'.str_repeat('../', $numParts).'index.html">'.$this->sourceDir.'</a></li> ';

        $folderUp = $numParts - 1;
        while ($label = array_shift($parts)) {
            if (!$parts) {
                $breadcrumbString .= '<li class="breadcrumb-item active">'.$label.'</li> ';
            } else {
                $link = './'.str_repeat('../', $folderUp).'index.html';
                $breadcrumbString .= sprintf('<li class="breadcrumb-item"><a href="%s">%s</a></li> ', $link, $label);
            }
            --$folderUp;
        }

        return $breadcrumbString;
    }
}
