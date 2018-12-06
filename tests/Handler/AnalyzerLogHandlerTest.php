<?php

namespace Scheb\Tombstone\Tests\Handler;

use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Handler\AnalyzerLogHandler;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Logging\AnalyzerLogFormatTest;

class AnalyzerLogHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private $logDir;

    public function setUp()
    {
        $this->logDir = __DIR__.'/../_logs/';
        $this->clearLogFiles();
    }

    public function tearDown()
    {
        $this->clearLogFiles();
    }

    private function clearLogFiles()
    {
        foreach ($this->readLogFiles() as $logFile) {
            unlink($logFile);
        }
    }

    /**
     * @return array
     */
    private function readLogFiles()
    {
        $handle = opendir($this->logDir);
        $logFiles = array();
        while ($file = readdir($handle)) {
            if ('.' == $file || '..' == $file || '.gitkeep' == $file) {
                continue;
            }

            $logFiles[] = $this->logDir.$file;
        }

        return $logFiles;
    }

    /**
     * @test
     */
    public function log_differentTombstones_twoLogFilesWritten()
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
    public function log_sizeLimitSet_stopWhenLimitExceeded()
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
    public function log_logWritten_isAnalyzerLogFormat()
    {
        $handler = new AnalyzerLogHandler($this->logDir);
        $handler->log(VampireFixture::getVampire());

        $logFiles = $this->readLogFiles();
        $this->assertCount(1, $logFiles);
        $logFileContent = file_get_contents($logFiles[0]);
        $this->assertEquals(AnalyzerLogFormatTest::getLog()."\n", $logFileContent);
    }
}
