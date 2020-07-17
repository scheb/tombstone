<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Graveyard;
use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Tests\Fixtures\TraceFixture;
use Scheb\Tombstone\Vampire;
use Scheb\Tombstone\VampireFactory;

class GraveyardTest extends TestCase
{
    /**
     * @var MockObject|VampireFactory
     */
    private $vampireFactory;

    /**
     * @var MockObject|HandlerInterface
     */
    private $handler;

    /**
     * @var Graveyard
     */
    private $graveyard;

    protected function setUp(): void
    {
        $this->vampireFactory = $this->createMock(VampireFactory::class);
        $this->handler = $this->getHandlerMock();
        $this->graveyard = new Graveyard($this->vampireFactory, null, [$this->handler]);
    }

    private function getHandlerMock(): MockObject
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @test
     */
    public function tombstone_traceGiven_createVampire(): void
    {
        $trace = TraceFixture::getTraceFixture();

        $this->vampireFactory
            ->expects($this->once())
            ->method('createFromCall')
            ->with(['label'], $trace, ['metaField' => 'metaValue'])
            ->willReturn($this->createMock(Vampire::class));

        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_handlersRegistered_callAllHandlers(): void
    {
        $vampire = $this->createMock(Vampire::class);
        $this->vampireFactory
            ->expects($this->any())
            ->method('createFromCall')
            ->willReturn($vampire);

        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->identicalTo($vampire));

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
