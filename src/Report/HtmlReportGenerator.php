<?php

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\Html\Exception\HtmlReportException;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DashboardRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileRenderer;

class HtmlReportGenerator implements ReportGeneratorInterface
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
     * @var string
     */
    private $templateDir;

    public function __construct(string $reportDir, string $sourceDir)
    {
        $this->reportDir = $reportDir;
        $this->sourceDir = $sourceDir;
        $this->templateDir = __DIR__.'/Html/Template';
    }

    public function generate(AnalyzerResult $result): void
    {
        $this->copySkeleton();

        $dashboardRenderer = new DashboardRenderer($this->reportDir, $this->sourceDir);
        $dashboardRenderer->generate($result);

        $directoryRenderer = new DirectoryRenderer($this->reportDir, $this->sourceDir);
        $directoryRenderer->generate($result);

        $fileRenderer = new FileRenderer($this->reportDir, $this->sourceDir);
        $fileRenderer->generate($result);
    }

    private function copySkeleton(): void
    {
        $this->createDirectory($this->reportDir);
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'css', $this->reportDir.DIRECTORY_SEPARATOR.'css');
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'fonts', $this->reportDir.DIRECTORY_SEPARATOR.'fonts');
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'img', $this->reportDir.DIRECTORY_SEPARATOR.'img');
    }

    private function copyDirectoryFiles(string $templateDir, string $reportDir): void
    {
        $this->createDirectory($reportDir);
        $handle = opendir($templateDir);
        while ($file = readdir($handle)) {
            if ('.' == $file || '..' == $file) {
                continue;
            }

            $templateFile = $templateDir.DIRECTORY_SEPARATOR.$file;
            $reportFile = $reportDir.DIRECTORY_SEPARATOR.$file;

            if (is_dir($templateFile)) {
                $this->copyDirectoryFiles($templateFile, $reportFile);
                continue;
            }

            if (!@copy($templateFile, $reportFile)) {
                throw new HtmlReportException('Could not copy '.$templateFile.' to '.$reportFile);
            }
        }
        closedir($handle);
    }

    private function createDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new HtmlReportException('Could not create directory '.$dir);
            }
        } elseif (!is_writable($dir)) {
            throw new HtmlReportException('Directory '.$dir.' has to be writable');
        }
    }
}
