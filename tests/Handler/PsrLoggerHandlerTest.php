<?php

namespace Scheb\Tombstone\Test\Handler;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Handler\PsrLoggerHandler;
use Scheb\Tombstone\Test\Fixtures\VampireFixture;
use Scheb\Tombstone\Test\Stubs\LabelFormatter;

class PsrLoggerHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function log_logMessageGiven_forwardToPsrLogger(): void
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('log')
            ->with('level', 'label');

        $handler = new PsrLoggerHandler($logger, 'level');
        $handler->setFormatter(new LabelFormatter());

        $handler->log(VampireFixture::getVampire());
    }
}
