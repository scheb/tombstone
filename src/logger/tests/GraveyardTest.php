<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Test\Fixtures\TraceFixture;
use Scheb\Tombstone\Vampire;

class GraveyardTest extends TestCase
{
    private const MAX_STACK_TRACE_DEPTH = 3;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * @var Graveyard
     */
    private $graveyard;

    protected function setUp()
    {
        $this->handler = $this->getHandlerMock();
        $this->graveyard = new Graveyard([$this->handler], null, self::MAX_STACK_TRACE_DEPTH);
    }

    private function getHandlerMock(): MockObject
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @test
     */
    public function addHandler_anotherHandler_isCalledOnTombstone(): void
    {
        $handler = $this->getHandlerMock();
        $this->graveyard->addHandler($handler);

        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->isInstanceOf(Vampire::class));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_handlersRegistered_callAllHandlers(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->isInstanceOf(Vampire::class));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_rootDirSet_logRelativePath(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($vampire) {
                return $vampire instanceof Vampire && 'file1.php' === $vampire->getFile();
            }));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->setRootDir('/path/to');
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_rootDirNotMatchedFilePath_logAbsolutePath(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($vampire) {
                return $vampire instanceof Vampire && '/path/to/file1.php' === $vampire->getFile();
            }));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->setRootDir('/other/path');
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function addHandler_largeTrace_limitStackTrace(): void
    {
        $handler = $this->getHandlerMock();
        $this->graveyard->addHandler($handler);

        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($vampire) {
                /* @var Vampire $vampire */
                $this->assertInstanceOf(Vampire::class, $vampire);
                $this->assertCount(self::MAX_STACK_TRACE_DEPTH, $vampire->getStackTrace(), 'Stack trace must be limited to '.self::MAX_STACK_TRACE_DEPTH.' frames');

                return true;
            }));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function flush_handlerRegistered_flushAllHandlers(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('flush');

        $this->graveyard->flush();
    }
}
