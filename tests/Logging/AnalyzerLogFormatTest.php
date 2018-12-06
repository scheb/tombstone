<?php

namespace Scheb\Tombstone\Test\Logging;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;

class AnalyzerLogFormatTest extends TestCase
{
    public const LOG_RECORD = '{"v":1,"d":"2014-01-01","a":"author","l":"label","f":"file","n":123,"m":"method","id":"2015-01-01","im":"invoker"}';

    /**
     * @test
     */
    public function vampireToLog_formatVampire_returnLogFormat(): void
    {
        $vampire = VampireFixture::getVampire();
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
        $expectedVampire = VampireFixture::getVampire();
        $this->assertEquals($expectedVampire, $returnValue);
    }
}
