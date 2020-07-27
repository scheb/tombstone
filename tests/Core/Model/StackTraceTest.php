<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Tests\TestCase;

class StackTraceTest extends TestCase
{
    /**
     * @test
     */
    public function getHash_framesProvided_returnCorrectHash(): void
    {
        $frame1 = $this->createMock(StackTraceFrame::class);
        $frame1->expects($this->any())->method('getHash')->willReturn(123);

        $frame2 = $this->createMock(StackTraceFrame::class);
        $frame2->expects($this->any())->method('getHash')->willReturn(456);

        $stackTrace = new StackTrace($frame1, $frame2);
        $this->assertEquals(488632175, $stackTrace->getHash());
    }
}
