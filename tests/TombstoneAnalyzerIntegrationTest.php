<?php

namespace Scheb\Tombstone\Analyzer\Test;

use Scheb\Tombstone\Analyzer\Analyzer;
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

    /**
     * @test
     */
    public function generate_logsAndSourceGiven_createHtmlReport()
    {
        $this->runTestApplication();
        $this->generateReport();
    }

    private function runTestApplication(): void
    {
        require_once __DIR__ . '/Fixtures/application.php';
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
        $tombstoneIndex = $sourceScanner->getTombstones(function() {});
        $logScanner = new LogDirectoryScanner(new LogReader(new VampireIndex()), self::LOG_DIR);
        $vampireIndex = $logScanner->getVampires(function () {});
        $analyzer = new Analyzer([
            new MethodNameStrategy(),
            new PositionStrategy(),
        ]);
        $result = $analyzer->getResult($tombstoneIndex, $vampireIndex);

        $report = new HtmlReportGenerator(self::REPORT_DIR, self::SOURCE_DIR);
        $report->generate($result);

        $this->expectNotToPerformAssertions();
    }
}
