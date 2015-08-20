<?php
namespace Scheb\Tombstone\Tests\Logging;

use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Vampire
     */
    public function getVampire()
    {
        $tombstone = new Tombstone('2014-01-01', 'author', 'label', 'file', 'line', 'method');
        return new Vampire('2015-01-01', 'invoker', $tombstone);
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return '{"v":1,"d":"2014-01-01","a":"author","l":"label","f":"file","n":"line","m":"method","id":"2015-01-01","im":"invoker"}';
    }

    /**
     * @test
     */
    public function vampireToLog()
    {
        $vampire = $this->getVampire();
        $returnValue = AnalyzerLogFormat::vampireToLog($vampire);
        $expectedLog = $this->getLog();
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
        $log = $this->getLog();
        $returnValue = AnalyzerLogFormat::logToVampire($log);
        $expectedVampire = $this->getVampire();
        $this->assertEquals($expectedVampire, $returnValue);
    }
}
