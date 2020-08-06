<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Tests\TestCase;

class StackTraceTest extends TestCase
{
    /**
     * @var StackTrace
     */
    private $stackTrace;

    protected function setUp(): void
    {
        $frame1 = $this->createMock(StackTraceFrame::class);
        $frame1->expects($this->any())->method('getHash')->willReturn(123);

        $frame2 = $this->createMock(StackTraceFrame::class);
        $frame2->expects($this->any())->method('getHash')->willReturn(456);

        $this->stackTrace = new StackTrace($frame1, $frame2);
    }

    /**
     * @test
     */
    public function getHash_framesProvided_returnCorrectHash(): void
    {
        $this->assertEquals(488632175, $this->stackTrace->getHash());
    }

    /**
     * @test
     */
    public function offsetExists_offsetIsSet_returnTrue(): void
    {
        $this->assertTrue(isset($this->stackTrace[1]));
    }

    /**
     * @test
     */
    public function offsetExists_offsetIsNotSet_returnFalse(): void
    {
        $this->assertFalse(isset($this->stackTrace[999]));
    }
}
