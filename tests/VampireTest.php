<?php

namespace Scheb\Tombstone\Test;

use Scheb\Tombstone\Test\Fixtures\TraceFixture;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireTest extends TestCase
{
    /**
     * @test
     */
    public function createFromCall_dataGiven_returnCorrectlyConstructedVampire(): void
    {
        $stackTrace = TraceFixture::getTraceFixture();
        $vampire = Vampire::createFromCall(['label', '2015-08-19'], $stackTrace);

        $this->assertInstanceOf(Vampire::class, $vampire);
        $this->assertInstanceOf(Tombstone::class, $vampire->getTombstone());
        $this->assertEquals(['label', '2015-08-19'], $vampire->getArguments());
        $this->assertEquals('2015-08-19', $vampire->getTombstoneDate());
        $this->assertEquals('/path/to/file1.php', $vampire->getFile());
        $this->assertEquals(11, $vampire->getLine());
        $this->assertEquals('containingMethodName', $vampire->getMethod());
        $this->assertEquals('invokerMethodName', $vampire->getInvoker());

        $invocationDate = strtotime($vampire->getInvocationDate());
        $this->assertEquals(time(), $invocationDate, '', 5);
    }
}
