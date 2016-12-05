<?php
namespace Scheb\Tombstone\Tests\Logging;

use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;

class AnalyzerLogFormatTest extends TestCase
{
    /**
     * @return string
     */
    public static function getLog()
    {
        return '{"v":1,"d":"2014-01-01","a":"author","l":"label","f":"file","n":"line","m":"method","id":"2015-01-01","im":"invoker"}';
    }

    /**
     * @test
     */
    public function vampireToLog()
    {
        $vampire = VampireFixture::getVampire();
        $returnValue = AnalyzerLogFormat::vampireToLog($vampire);
        $expectedLog = self::getLog();
        $this->assertEquals($returnValue, $expectedLog);
    }

    /**
     * @test
     */
    public function logToVampire_invalidLog_returnNull()
    {
        $returnValue = AnalyzerLogFormat::logToVampire('invalid');
        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function logToVampire_validLog_returnVampire()
    {
        $log = self::getLog();
        $returnValue = AnalyzerLogFormat::logToVampire($log);
        $expectedVampire = VampireFixture::getVampire();
        $this->assertEquals($expectedVampire, $returnValue);
    }
}
