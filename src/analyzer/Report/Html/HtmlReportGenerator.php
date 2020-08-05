<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Html;

use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DashboardRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileRenderer;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;

class HtmlReportGenerator implements ReportGeneratorInterface
{
    private const TEMPLATE_DIR = __DIR__.'/Template';

    /**
     * @var string
     */
    private $reportDir;

    /**
     * @var \Scheb\Tombstone\Analyzer\Util\Scheb\Tombstone\Analyzer\Report\FileSystem
     */
    private $fileSystem;

    /**
     * @var DashboardRenderer
     */
    private $dashboardRenderer;

    /**
     * @var DirectoryRenderer
     */
    private $directoryRenderer;

    /**
     * @var FileRenderer
     */
    private $fileRenderer;

    public function __construct(string $reportDir, FileSystem $fileSystem, DashboardRenderer $dashboardRenderer, DirectoryRenderer $directoryRenderer, FileRenderer $fileRenderer)
    {
        $this->reportDir = $reportDir;
        $this->fileSystem = $fileSystem;
        $this->dashboardRenderer = $dashboardRenderer;
        $this->directoryRenderer = $directoryRenderer;
        $this->fileRenderer = $fileRenderer;
    }

    public function getName(): string
    {
        return 'HTML';
    }

    public function generate(AnalyzerResult $result): void
    {
        $this->copySkeleton();
        $this->dashboardRenderer->generate($result);
        $this->directoryRenderer->generate($result);
        $this->fileRenderer->generate($result);
    }

    private function copySkeleton(): void
    {
        $this->fileSystem->ensureDirectoryCreated($this->reportDir);

        $this->fileSystem->copyDirectoryFiles(
            FileSystem::createPath(self::TEMPLATE_DIR, 'css'),
            FileSystem::createPath($this->reportDir, '_css')
        );
        $this->fileSystem->copyDirectoryFiles(
            FileSystem::createPath(self::TEMPLATE_DIR, 'icons'),
            FileSystem::createPath($this->reportDir, '_icons')
        );
        $this->fileSystem->copyDirectoryFiles(
            FileSystem::createPath(self::TEMPLATE_DIR, 'img'),
            FileSystem::createPath($this->reportDir, '_img')
        );
    }
}
