<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test;

use Scheb\Tombstone\StackTraceFrame;
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
        $metadata = ['metaField' => 'metaValue'];
        $vampire = Vampire::createFromCall(['label', '2015-08-19'], $stackTrace, $metadata);

        $this->assertInstanceOf(Vampire::class, $vampire);
        $this->assertInstanceOf(Tombstone::class, $vampire->getTombstone());
        $this->assertEquals(['label', '2015-08-19'], $vampire->getArguments());
        $this->assertEquals('2015-08-19', $vampire->getTombstoneDate());
        $this->assertEquals('/path/to/file1.php', $vampire->getFile());
        $this->assertEquals(11, $vampire->getLine());
        $this->assertEquals($metadata, $vampire->getMetadata());
        $this->assertEquals('containingMethodName', $vampire->getMethod());
        $this->assertEquals('invokerMethodName', $vampire->getInvoker());

        $stackTrace = $vampire->getStackTrace();
        $this->assertCount(TraceFixture::NUMBER_OF_FRAMES, $stackTrace);

        $expectedFrame = new StackTraceFrame('C:/path/to/file4.php', 44, 'ClassName->invokerInvokerMethodName');
        $this->assertEquals($expectedFrame, $stackTrace[3]);

        $this->assertEquals('e5596f50384552bf050040bcf28763d8fdd87c72', $vampire->getStackTraceHash());

        $invocationDate = strtotime($vampire->getInvocationDate());
        $this->assertEquals(time(), $invocationDate, '', 5);
    }
}
