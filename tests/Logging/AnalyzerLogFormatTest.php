<?php

namespace Scheb\Tombstone\Test\Logging;

use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\TestCase;

class AnalyzerLogFormatTest extends TestCase
{
    public const TOMBSTONE_ARGUMENTS = ['2014-01-01', 'label'];
    public const LOG_RECORD = '{"v":3,"a":["2014-01-01","label"],"f":"file","n":123,"m":"method","d":{"metaField":"metaValue"},"id":"2015-01-01","im":"invoker"}';

    /**
     * @test
     */
    public function vampireToLog_formatVampire_returnLogFormat(): void
    {
        $vampire = VampireFixture::getVampire(...self::TOMBSTONE_ARGUMENTS);
        $returnValue = AnalyzerLogFormat::vampireToLog($vampire);
        $this->assertEquals($returnValue, self::LOG_RECORD);
    }

    /**
     * @test
     */
    public function logToVampire_invalidLog_returnNull(): void
    {
        $returnValue = AnalyzerLogFormat::logToVampire('invalid');
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function logToVampire_validLog_returnVampire(): void
    {
        $returnValue = AnalyzerLogFormat::logToVampire(self::LOG_RECORD);
        $expectedVampire = VampireFixture::getVampire(...self::TOMBSTONE_ARGUMENTS);
        $this->assertEquals($expectedVampire, $returnValue);
    }
}
