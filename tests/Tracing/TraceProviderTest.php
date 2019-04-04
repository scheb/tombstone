<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Tracing;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Tracing\TraceProvider;

class TraceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getTraceHere_traceContainingFunction_returnStackTrace(): void
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
