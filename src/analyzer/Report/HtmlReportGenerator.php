<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
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
    private $rootDir;

    /**
     * @var string
     */
    private $templateDir;

    public function __construct(string $reportDir, string $rootDir)
    {
        $this->reportDir = $reportDir;
        $this->rootDir = $rootDir;
        $this->templateDir = __DIR__.'/Html/Template';
    }

    public function getName(): string
    {
        return 'HTML';
    }

    public function generate(AnalyzerResult $result): void
    {
        $this->copySkeleton();

        $dashboardRenderer = new DashboardRenderer($this->reportDir, $this->rootDir);
        $dashboardRenderer->generate($result);

        $directoryRenderer = new DirectoryRenderer($this->reportDir, $this->rootDir);
        $directoryRenderer->generate($result);

        $fileRenderer = new FileRenderer($this->reportDir, $this->rootDir);
        $fileRenderer->generate($result);
    }

    private function copySkeleton(): void
    {
        $this->createDirectory($this->reportDir);
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'css', $this->reportDir.DIRECTORY_SEPARATOR.'css');
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'icons', $this->reportDir.DIRECTORY_SEPARATOR.'icons');
        $this->copyDirectoryFiles($this->templateDir.DIRECTORY_SEPARATOR.'img', $this->reportDir.DIRECTORY_SEPARATOR.'img');
    }

    private function copyDirectoryFiles(string $templateDir, string $reportDir): void
    {
        $this->createDirectory($reportDir);
        $handle = opendir($templateDir);
        if (!$handle) {
            throw new HtmlReportException(sprintf('Could not read template files from %s', $templateDir));
        }

        while ($file = readdir($handle)) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $templateFile = $templateDir.DIRECTORY_SEPARATOR.$file;
            $reportFile = $reportDir.DIRECTORY_SEPARATOR.$file;

            if (is_dir($templateFile)) {
                $this->copyDirectoryFiles($templateFile, $reportFile);
                continue;
            }

            if (!@copy($templateFile, $reportFile)) {
                throw new HtmlReportException(sprintf('Could not copy %s to %s', $templateFile, $reportFile));
            }
        }
        closedir($handle);
    }

    private function createDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new HtmlReportException(sprintf('Could not create directory %s', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new HtmlReportException(sprintf('Directory %s has to be writable', $dir));
        }
    }
}
