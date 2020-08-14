<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Tracing;

use Scheb\Tombstone\Logger\Tracing\TraceProvider;
use Scheb\Tombstone\Tests\TestCase;

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

        $this->assertIsArray($trace);

        // At least 2 because there must be a frame for this method here and one for the calling method from PHPUnit
        $this->assertGreaterThanOrEqual(2, \count($trace));

        $firstFrame = array_shift($trace);
        $this->assertEquals($thisMethod, $firstFrame['function']);
        $this->assertEquals($thisClass, $firstFrame['class']);
    }
}
