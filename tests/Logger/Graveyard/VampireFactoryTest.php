<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Logger\Graveyard;

use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Logger\Graveyard\VampireFactory;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

class VampireFactoryTest extends TestCase
{
    private const LONG_STACK_TRACE_DEPTH = 99999;

    /**
     * @test
     */
    public function createFromCall_dataGiven_returnCorrectlyConstructedVampire(): void
    {
        $factory = new VampireFactory(new RootPath('/root'), self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = Fixture::getTraceFixture();
        $metadata = ['metaField' => 'metaValue'];
        $vampire = $factory->createFromCall(['label', '2015-08-19'], $stackTrace, $metadata);

        $this->assertInstanceOf(Vampire::class, $vampire);
        $this->assertInstanceOf(Tombstone::class, $vampire->getTombstone());
        $this->assertEquals(['label', '2015-08-19'], $vampire->getArguments());
        $this->assertEquals('2015-08-19', $vampire->getTombstoneDate());
        $this->assertEquals('/path/to/file1.php', $vampire->getFile()->getAbsolutePath());
        $this->assertEquals(11, $vampire->getLine());
        $this->assertEquals($metadata, $vampire->getMetadata());
        $this->assertEquals('containingMethodName', $vampire->getMethod());
        $this->assertEquals('invokerMethodName', $vampire->getInvoker());

        $stackTrace = $vampire->getStackTrace();
        $this->assertCount(Fixture::NUMBER_OF_FRAMES, $stackTrace);

        $frame = $stackTrace[3];
        $this->assertEquals('C:/path/to/file4.php', $frame->getFile()->getAbsolutePath());
        $this->assertEquals(44, $frame->getLine());
        $this->assertEquals('ClassName->invokerInvokerMethodName', $frame->getMethod());

        $invocationDate = strtotime($vampire->getInvocationDate());
        $this->assertEquals(time(), $invocationDate);
        $this->assertEqualsWithDelta(time(), $invocationDate, 5);
    }

    /**
     * @test
     */
    public function createFromCall_rootDirSetMatchesFilePath_logRelativePath(): void
    {
        $factory = new VampireFactory(new RootPath(Fixture::ROOT_DIR), self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = Fixture::getTraceFixture();
        $vampire = $factory->createFromCall([], $stackTrace, []);

        $this->assertEquals('file1.php', $vampire->getFile()->getReferencePath());

        $stackTrace = $vampire->getStackTrace();
        $this->assertEquals('file1.php', $stackTrace[0]->getFile()->getReferencePath());
        $this->assertEquals('file2.php', $stackTrace[1]->getFile()->getReferencePath());
        $this->assertEquals('file3.php', $stackTrace[2]->getFile()->getReferencePath());
    }

    /**
     * @test
     */
    public function createFromCall_rootDirNotMatchedFilePath_logAbsolutePath(): void
    {
        $factory = new VampireFactory(new RootPath('/other/path'), self::LONG_STACK_TRACE_DEPTH);

        $stackTrace = Fixture::getTraceFixture();
        $vampire = $factory->createFromCall(['label'], $stackTrace, []);

        $this->assertEquals('/path/to/file1.php', $vampire->getFile()->getAbsolutePath());

        $stackTrace = $vampire->getStackTrace();
        $this->assertEquals('/path/to/file1.php', $stackTrace[0]->getFile()->getAbsolutePath());
        $this->assertEquals('/path/to/file2.php', $stackTrace[1]->getFile()->getAbsolutePath());
        $this->assertEquals('/path/to/file3.php', $stackTrace[2]->getFile()->getAbsolutePath());
    }

    /**
     * @test
     */
    public function createFromCall_largeTrace_limitStackTrace(): void
    {
        $factory = new VampireFactory(new RootPath(__DIR__), 2);

        $stackTrace = Fixture::getTraceFixture();
        $vampire = $factory->createFromCall([], $stackTrace, []);

        $stackTrace = $vampire->getStackTrace();
        $this->assertCount(2, $stackTrace);
    }

    /**
     * @test
     * @dataProvider getTraceToTestTombstoneFunctionName
     */
    public function createFromCall_traceGiven_extractTombstoneFunctionName(array $stackTrace, string $expectedFunctionName): void
    {
        $factory = new VampireFactory(new RootPath('/root'), 0);

        $vampire = $factory->createFromCall([], $stackTrace, []);

        $this->assertEquals($expectedFunctionName, $vampire->getTombstone()->getFunctionName());
    }

    public function getTraceToTestTombstoneFunctionName(): array
    {
        return [
            [[['file' => 'file.php', 'line' => 1, 'function' => 'tombstone']], 'tombstone'],
            [[['file' => 'file.php', 'line' => 1, 'function' => 'Namespace\\tombstone']], 'Namespace\\tombstone'],
            [[['file' => 'file.php', 'line' => 1, 'class' => 'Namespace\\Class', 'type' => '::', 'function' => 'tombstone']], 'Namespace\\Class::tombstone'],
            [[['file' => 'file.php', 'line' => 1, 'class' => 'Namespace\\Class', 'type' => '->', 'function' => 'tombstone']], 'Namespace\\Class::tombstone'],
        ];
    }
}
