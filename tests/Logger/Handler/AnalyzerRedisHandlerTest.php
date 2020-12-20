<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Handler;

use Redis;
use Scheb\Tombstone\Logger\Handler\AnalyzerRedisHandler;
use Scheb\Tombstone\Tests\Core\Format\AnalyzerLogFormatTest;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerRedisHandlerTest extends TestCase
{
    /**
     * @var Redis
     */
    private static $redis;

    public static function setUpBeforeClass()
    {
        self::$redis = new Redis();
        self::$redis->connect('tombstone-redis-test');
    }

    protected function setUp(): void
    {
        $this->clearLogFiles();
    }

    public function tearDown(): void
    {
        $this->clearLogFiles();
    }

    private function clearLogFiles(): void
    {
        $logFiles = self::$redis->keys('*');

        self::$redis->del($logFiles[0], ...array_splice($logFiles, 1));
    }

    private function getLogFiles(): array
    {
        return self::$redis->keys('*');
    }

    private function readLogFiles(array $logFiles): string
    {
        $logFileContent = "";
        foreach (self::$redis->xRead([$logFiles[0] => '0-0']) as $rows) {
            foreach ($rows as $timestamp => $row) {
                $logFileContent .= $row['data'];
            }
        }

        return $logFileContent;
    }

    /**
     * @test
     */
    public function log_differentTombstones_twoLogFilesWritten(): void
    {
        $handler = new AnalyzerRedisHandler(self::$redis);
        $handler->log(Fixture::getVampire('2015-01-01'));
        $handler->log(Fixture::getVampire('2014-01-01'));

        $logFiles = $this->getLogFiles();
        $this->assertCount(2, $logFiles);
    }

    /**
     * @test
     */
    public function log_sizeLimitSet_stopWhenLimitExceeded(): void
    {
        $handler = new AnalyzerRedisHandler(self::$redis, 500);
        for ($i = 0; $i < 1000; $i++) {
            $handler->log(Fixture::getVampire('2015-01-01'));
        }

        $logFiles = $this->getLogFiles();
        $lineCount = substr_count($this->readLogFiles($logFiles), "\n");

        $this->assertCount(1, $logFiles);
        $this->assertLessThan(500 + 500, $lineCount);
    }

    /**
     * @test
     */
    public function log_logWritten_isAnalyzerLogFormat(): void
    {
        $handler = new AnalyzerRedisHandler(self::$redis);
        $handler->log(Fixture::getVampire(...AnalyzerLogFormatTest::TOMBSTONE_ARGUMENTS));

        $logFiles = $this->getLogFiles();
        $this->assertCount(1, $logFiles);

        $logFileContent = $this->readLogFiles($logFiles);
        $this->assertEquals(AnalyzerLogFormatTest::LOG_RECORD.PHP_EOL, $logFileContent);
    }
}
