<?php
namespace Scheb\Tombstone\Tests\Tracing;

use Scheb\Tombstone\Tracing\TraceProvider;

class TraceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getTraceHere_traceContainingFunction_returnStackTrace()
    {
        $thisMethod = __FUNCTION__;
        $thisClass = __CLASS__;

        $trace = TraceProvider::getTraceHere();
        $this->assertInternalType('array', $trace);
        $this->assertGreaterThanOrEqual(3, $trace);
        $this->assertEquals($thisMethod, $trace[0]['function']);
        $this->assertEquals($thisClass, $trace[0]['class']);
    }
}
