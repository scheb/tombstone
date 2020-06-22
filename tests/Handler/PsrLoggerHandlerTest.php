<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Handler;

use Psr\Log\LoggerInterface;
use Scheb\Tombstone\Handler\PsrLoggerHandler;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Stubs\LabelFormatter;
use Scheb\Tombstone\Tests\TestCase;

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
