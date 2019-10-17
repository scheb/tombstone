<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Handler;

use Scheb\Tombstone\Handler\AnalyzerLogHandler;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Logging\AnalyzerLogFormatTest;
use Scheb\Tombstone\Test\TestCase;

class AnalyzerLogHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private $logDir;

    protected function setUp()
    {
        $this->logDir = __DIR__.'/../_logs/';
        $this->clearLogFiles();
    }

    public function tearDown()
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
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2014-01-01'));

        $logFiles = $this->readLogFiles();
        $this->assertCount(2, $logFiles);
    }

    /**
     * @test
     */
    public function log_sizeLimitSet_stopWhenLimitExceeded(): void
    {
        $handler = new AnalyzerLogHandler($this->logDir, 128);
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));
        $handler->log(VampireFixture::getVampire('2015-01-01'));

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
        $handler->log(VampireFixture::getVampire(...AnalyzerLogFormatTest::TOMBSTONE_ARGUMENTS));

        $logFiles = $this->readLogFiles();
        $this->assertCount(1, $logFiles);
        $logFileContent = file_get_contents($logFiles[0]);
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD."\n", $logFileContent);
    }
}
