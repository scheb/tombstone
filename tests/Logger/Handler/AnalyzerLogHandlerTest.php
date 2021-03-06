<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Handler;

use Scheb\Tombstone\Logger\Handler\AnalyzerLogHandler;
use Scheb\Tombstone\Tests\Core\Format\AnalyzerLogFormatTest;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerLogHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private $logDir;

    protected function setUp(): void
    {
        $this->logDir = __DIR__.'/../../../app/logs/';
        $this->clearLogFiles();
    }

    public function tearDown(): void
    {
        $this->clearLogFiles();
    }

    private function clearLogFiles(): void
    {
        foreach ($this->readLogFiles() as $logFile) {
            unlink($logFile);
        }
    }

    private function readLogFiles(): array
    {
        $handle = opendir($this->logDir);
        $logFiles = [];
        while ($file = readdir($handle)) {
            if ('.' === $file || '..' === $file || '.gitkeep' === $file) {
                continue;
            }

            $logFiles[] = $this->logDir.$file;
        }

        return $logFiles;
    }

    /**
     * @test
     */
    public function log_differentTombstones_twoLogFilesWritten(): void
    {
        $handler = new AnalyzerLogHandler($this->logDir);
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2014-01-01'));

        $logFiles = $this->readLogFiles();
        $this->assertCount(2, $logFiles);
    }

    /**
     * @test
     */
    public function log_sizeLimitSet_stopWhenLimitExceeded(): void
    {
        $handler = new AnalyzerLogHandler($this->logDir, 128);
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2015-01-01'));

        $logFiles = $this->readLogFiles();
        $this->assertCount(1, $logFiles);
        $this->assertLessThan(128 + 128, filesize($logFiles[0]));
    }

    /**
     * @test
     */
    public function log_logWritten_isAnalyzerLogFormat(): void
    {
        $handler = new AnalyzerLogHandler($this->logDir);
        $handler->log(Fixture::getVampire(...AnalyzerLogFormatTest::TOMBSTONE_ARGUMENTS));

        $logFiles = $this->readLogFiles();
        $this->assertCount(1, $logFiles);
        $logFileContent = file_get_contents($logFiles[0]);
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD.PHP_EOL, $logFileContent);
    }
}
