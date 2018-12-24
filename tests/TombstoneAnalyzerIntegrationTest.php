<?php

namespace Scheb\Tombstone\Analyzer\Test;

use Scheb\Tombstone\Analyzer\Analyzer;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Log\LogDirectoryScanner;
use Scheb\Tombstone\Analyzer\Log\LogReader;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Analyzer\Report\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Source\SourceDirectoryScanner;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorFactory;
use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Analyzer\VampireIndex;

class TombstoneAnalyzerIntegrationTest extends TestCase
{
    private const SOURCE_DIR = __DIR__.'/Fixtures';
    private const LOG_DIR = __DIR__.'/_logs';
    private const REPORT_DIR = __DIR__.'/_report';
    private const EXPECTED_REPORT_FILES = [
        '.gitkeep',
        'App',
        'App/SampleClass.php.html',
        'App/index.html',
        'css',
        'css/bootstrap.min.css',
        'css/style.css',
        'dashboard.html',
        'fonts',
        'fonts/glyphicons-halflings-regular.eot',
        'fonts/glyphicons-halflings-regular.svg',
        'fonts/glyphicons-halflings-regular.ttf',
        'fonts/glyphicons-halflings-regular.woff',
        'fonts/glyphicons-halflings-regular.woff2',
        'functions.php.html',
        'img',
        'img/cross.png',
        'img/deleted.png',
        'img/vampire.png',
        'index.html',
    ];

    /**
     * @var AnalyzerResult
     */
    private $analyzerResult;

    /**
     * @test
     * @coversNothing
     */
    public function generate_logsAndSourceGiven_createHtmlReport()
    {
        $this->runTestApplication();
        $this->generateReport();
        $this->assertReportFileStructure();
        $this->assertReportResult();
    }

    private function runTestApplication(): void
    {
        require_once __DIR__.'/Fixtures/run.php';
    }

    private function generateReport(): void
    {
        $sourceScanner = new SourceDirectoryScanner(
            TombstoneExtractorFactory::create(
                new TombstoneIndex(self::SOURCE_DIR)
            ),
            self::SOURCE_DIR,
            ['*.php']
        );
        $tombstoneIndex = $sourceScanner->getTombstones(function () {});
        $logScanner = new LogDirectoryScanner(new LogReader(new VampireIndex()), self::LOG_DIR);
        $vampireIndex = $logScanner->getVampires(function () {});
        $analyzer = new Analyzer([
            new MethodNameStrategy(),
            new PositionStrategy(),
        ]);
        $this->analyzerResult = $analyzer->getResult($tombstoneIndex, $vampireIndex);

        $report = new HtmlReportGenerator(self::REPORT_DIR, self::SOURCE_DIR);
        $report->generate($this->analyzerResult);
    }

    private function assertReportFileStructure(): void
    {
        $directoryListing = $this->listReportDirectory();
        $this->assertEquals(self::EXPECTED_REPORT_FILES, $directoryListing);
    }

    private function listReportDirectory(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::REPORT_DIR, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $relativePathStart = strlen(self::REPORT_DIR) + 1;
        $files = [];
        foreach ($iterator as $fileInfo) {
            $files[] = str_replace('\\', '/', substr($fileInfo->getRealPath(), $relativePathStart));
        }
        sort($files);

        return $files;
    }

    private function assertReportResult(): void
    {
        $this->assertEquals(2, $this->analyzerResult->getDeadCount());
        $this->assertEquals(4, $this->analyzerResult->getUndeadCount());
        $this->assertEquals(0, $this->analyzerResult->getDeletedCount());
    }

    public function tearDown()
    {
        $this->clearDirectory(self::LOG_DIR);
        $this->clearDirectory(self::REPORT_DIR);
    }

    private function clearDirectory(string $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileInfo) {
            if ('.gitkeep' == $fileInfo->getBaseName()) {
                continue;
            }
            $cmd = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            @$cmd($fileInfo->getRealPath());
        }
    }
}
