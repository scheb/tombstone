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

    /**
     * @param string $reportDir
     * @param string $sourceDir
     */
    public function __construct($reportDir, $sourceDir)
    {
        $this->reportDir = $reportDir;
        $this->sourceDir = $sourceDir;
        $this->templateDir = __DIR__ . '/Html/Template';
    }

    /**
     * @param AnalyzerResult $result
     */
    public function generate(AnalyzerResult $result)
    {
        $this->copySkeleton();

        $dashboardRenderer = new DashboardRenderer($this->reportDir, $this->sourceDir);
        $dashboardRenderer->generate($result);

        $directoryRenderer = new DirectoryRenderer($this->reportDir, $this->sourceDir);
        $directoryRenderer->generate($result);

        $fileRenderer = new FileRenderer($this->reportDir, $this->sourceDir);
        $fileRenderer->generate($result);
    }

    private function copySkeleton()
    {
        $this->createDirectory($this->reportDir);
        $this->copyDirectoryFiles($this->templateDir . DIRECTORY_SEPARATOR . 'css', $this->reportDir . DIRECTORY_SEPARATOR . 'css');
        $this->copyDirectoryFiles($this->templateDir . DIRECTORY_SEPARATOR . 'fonts', $this->reportDir . DIRECTORY_SEPARATOR . 'fonts');
        $this->copyDirectoryFiles($this->templateDir . DIRECTORY_SEPARATOR . 'img', $this->reportDir . DIRECTORY_SEPARATOR . 'img');
    }

    /**
     * @param string $templateDir
     * @param string $reportDir
     *
     * @throws HtmlReportException
     */
    private function copyDirectoryFiles($templateDir, $reportDir)
    {
        $this->createDirectory($reportDir);
        $handle = opendir($templateDir);
        while ($file = readdir($handle)) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $templateFile = $templateDir . DIRECTORY_SEPARATOR . $file;
            $reportFile = $reportDir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($templateFile)) {
                $this->copyDirectoryFiles($templateFile, $reportFile);
                continue;
            }

            if (!@copy($templateFile, $reportFile)) {
                throw new HtmlReportException('Could not copy ' . $templateFile . ' to ' . $reportFile);
            }
        }
        closedir($handle);
    }

    /**
     * @param string $dir
     *
     * @throws HtmlReportException
     */
    private function createDirectory($dir) {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new HtmlReportException('Could not create directory ' . $dir);
            }
        } else if (!is_writable($dir)) {
            throw new HtmlReportException('Directory ' . $dir . ' has to be writable');
        }
    }
}
