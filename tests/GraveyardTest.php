<?php

namespace Scheb\Tombstone\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Test\Fixtures\TraceFixture;
use Scheb\Tombstone\Vampire;

class GraveyardTest extends TestCase
{
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
        $this->graveyard = new Graveyard([$this->handler]);
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
        $this->graveyard->tombstone('date', 'author', 'label', $trace);
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
        $this->graveyard->tombstone('date', 'author', 'label', $trace);
    }

    /**
     * @test
     */
    public function tombstone_sourceDirSet_logRelativePath(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($vampire) {
                return $vampire instanceof Vampire && 'file1.php' === $vampire->getFile();
            }));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->setSourceDir('/path/to');
        $this->graveyard->tombstone('date', 'author', 'label', $trace);
    }

    /**
     * @test
     */
    public function tombstone_sourceDirNotMatchedFilePath_logAbsolutePath(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($vampire) {
                return $vampire instanceof Vampire && '/path/to/file1.php' === $vampire->getFile();
            }));

        $trace = TraceFixture::getTraceFixture();
        $this->graveyard->setSourceDir('/other/path');
        $this->graveyard->tombstone('date', 'author', 'label', $trace);
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
