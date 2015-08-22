<?php
namespace Scheb\Tombstone\Tests\Handler;

use Scheb\Tombstone\Handler\PsrLoggerHandler;
use Scheb\Tombstone\Tests\Fixtures\VampireFixture;
use Scheb\Tombstone\Tests\Stubs\LabelFormatter;

class PsrLoggerHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function log()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('log')
            ->with('level', 'label');

        $handler = new PsrLoggerHandler($logger, 'level');
        $handler->setFormatter(new LabelFormatter());

        $handler->log(VampireFixture::getVampire());
    }
}
