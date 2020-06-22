<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Handler;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Handler\PsrLoggerHandler;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Stubs\LabelFormatter;
use Scheb\Tombstone\Test\TestCase;

class PsrLoggerHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function log_logMessageGiven_forwardToPsrLogger(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('log')
            ->with('level', 'label');

        $handler = new PsrLoggerHandler($logger, 'level');
        $handler->setFormatter(new LabelFormatter());

        $handler->log(VampireFixture::getVampire('label'));
    }
}
