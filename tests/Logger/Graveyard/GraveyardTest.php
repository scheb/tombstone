<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Graveyard;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Graveyard\Graveyard;
use Scheb\Tombstone\Logger\Graveyard\VampireFactory;
use Scheb\Tombstone\Logger\Handler\HandlerInterface;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

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
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var Graveyard
     */
    private $graveyard;

    protected function setUp(): void
    {
        $this->vampireFactory = $this->createMock(VampireFactory::class);
        $this->handler = $this->getHandlerMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->graveyard = new Graveyard($this->vampireFactory, $this->logger, [$this->handler]);
    }

    private function getHandlerMock(): MockObject
    {
        return $this->createMock(HandlerInterface::class);
    }

    /**
     * @return MockObject|Vampire
     */
    private function stubVampireFactory(): MockObject
    {
        $vampire = $this->createMock(Vampire::class);
        $this->vampireFactory
            ->expects($this->any())
            ->method('createFromCall')
            ->willReturn($vampire);

        return $vampire;
    }

    /**
     * @test
     */
    public function tombstone_traceGiven_createVampire(): void
    {
        $trace = Fixture::getTraceFixture();

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
        $vampire = $this->stubVampireFactory();

        $this->handler
            ->expects($this->once())
            ->method('log')
            ->with($this->identicalTo($vampire));

        $trace = Fixture::getTraceFixture();
        $this->graveyard->tombstone(['label'], $trace, ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function tombstone_exceptionHappened_logError(): void
    {
        $this->stubVampireFactory();

        $this->handler
            ->expects($this->once())
            ->method('log')
            ->willThrowException(new \Exception('message', 123));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception while tracking a tombstone call: Exception message (123)');

        $trace = Fixture::getTraceFixture();
        $this->graveyard->tombstone([], $trace, []);
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

    /**
     * @test
     */
    public function flush_exceptionHappened_logError(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('flush')
            ->willThrowException(new \Exception('message', 123));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception while flushing tombstones: Exception message (123)');

        $this->graveyard->flush();
    }
}
