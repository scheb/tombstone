<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Model;

use Scheb\Tombstone\Model\StackTraceFrame;
use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;
use Scheb\Tombstone\Model\VampireFactory;
use Scheb\Tombstone\Tests\StackTraceFixture;
use Scheb\Tombstone\Tests\TestCase;

class VampireFactoryTest extends TestCase
{
    private const LONG_STACK_TRACE_DEPTH = 99999;

    /**
     * @test
     */
    public function createFromCall_dataGiven_returnCorrectlyConstructedVampire(): void
    {
        $factory = new VampireFactory(null, self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = StackTraceFixture::getTraceFixture();
        $metadata = ['metaField' => 'metaValue'];
        $vampire = $factory->createFromCall(['label', '2015-08-19'], $stackTrace, $metadata);

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
        $this->assertCount(StackTraceFixture::NUMBER_OF_FRAMES, $stackTrace);

        $expectedFrame = new StackTraceFrame('C:/path/to/file4.php', 44, 'ClassName->invokerInvokerMethodName');
        $this->assertEquals($expectedFrame, $stackTrace[3]);

        $this->assertEquals('3b5bcf293cabca79ca8f7b9e64643c2c0bedae41', $vampire->getStackTraceHash());

        $invocationDate = strtotime($vampire->getInvocationDate());
        $this->assertEquals(time(), $invocationDate);
        $this->assertEqualsWithDelta(time(), $invocationDate, 5);
    }

    /**
     * @test
     */
    public function createFromCall_rootDirSetMatchesFilePath_logRelativePath(): void
    {
        $factory = new VampireFactory(StackTraceFixture::ROOT_DIR, self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = StackTraceFixture::getTraceFixture();
        $vampire = $factory->createFromCall([], $stackTrace, []);

        $this->assertEquals('file1.php', $vampire->getFile());

        $stackTrace = $vampire->getStackTrace();
        $this->assertEquals('file1.php', $stackTrace[0]->getFile());
        $this->assertEquals('file2.php', $stackTrace[1]->getFile());
        $this->assertEquals('file3.php', $stackTrace[2]->getFile());
    }

    /**
     * @test
     */
    public function createFromCall_rootDirNotMatchedFilePath_logAbsolutePath(): void
    {
        $factory = new VampireFactory('/other/path', self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = StackTraceFixture::getTraceFixture();
        $vampire = $factory->createFromCall(['label'], $stackTrace, []);

        $this->assertEquals('/path/to/file1.php', $vampire->getFile());

        $stackTrace = $vampire->getStackTrace();
        $this->assertEquals('/path/to/file1.php', $stackTrace[0]->getFile());
        $this->assertEquals('/path/to/file2.php', $stackTrace[1]->getFile());
        $this->assertEquals('/path/to/file3.php', $stackTrace[2]->getFile());
    }

    /**
     * @test
     */
    public function createFromCall_largeTrace_limitStackTrace(): void
    {
        $factory = new VampireFactory(null, 2);

        $stackTrace = StackTraceFixture::getTraceFixture();
        $vampire = $factory->createFromCall([], $stackTrace, []);

        $stackTrace = $vampire->getStackTrace();
        $this->assertCount(2, $stackTrace);
    }
}
