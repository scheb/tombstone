<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Scheb\Tombstone\BufferedGraveyard;
use Scheb\Tombstone\GraveyardBuilder;
use Scheb\Tombstone\GraveyardRegistry;
use Scheb\Tombstone\Handler\HandlerInterface;
use Scheb\Tombstone\Tests\Fixtures\StackTraceFixture;
use Scheb\Tombstone\Vampire;

class GraveyardBuilderTest extends TestCase
{
    /**
     * @var GraveyardBuilder
     */
    private $builder;

    public function setUp(): void
    {
        $this->builder = new GraveyardBuilder();
    }

    private function assertStackTraceLength(int $expectedLength): \Closure
    {
        return function (Vampire $vampire) use ($expectedLength): bool {
            $this->assertCount($expectedLength, $vampire->getStackTrace());

            return true;
        };
    }

    private function assertRelativeFilePath(): \Closure
    {
        return function (Vampire $vampire): bool {
            $this->assertEquals('file1.php', $vampire->getFile());

            return true;
        };
    }

    /**
     * @test
     */
    public function build_withHandler_logTombstonesToHandler(): void
    {
        $handler = $this->createMock(HandlerInterface::class);
        $graveyard = $this->builder->withHandler($handler)->build();

        $handler
            ->expects($this->once())
            ->method('log');

        $graveyard->tombstone([], StackTraceFixture::getTraceFixture(), []);
    }

    /**
     * @test
     */
    public function build_withLogger_logExceptionsToLogger(): void
    {
        $handler = $this->createMock(HandlerInterface::class);
        $handler
            ->expects($this->any())
            ->method('log')
            ->willThrowException(new \Exception());

        $logger = $this->createMock(LoggerInterface::class);
        $graveyard = $this->builder
            ->withHandler($handler)
            ->withLogger($logger)
            ->build();

        $logger
            ->expects($this->once())
            ->method('error');

        $graveyard->tombstone([], StackTraceFixture::getTraceFixture(), []);
    }

    /**
     * @test
     */
    public function build_stackTraceDepthSet_logTruncatedStackTrace(): void
    {
        $handler = $this->createMock(HandlerInterface::class);
        $handler
            ->expects($this->any())
            ->method('log')
            ->with($this->callback($this->assertStackTraceLength(2)));

        $graveyard = $this->builder
            ->withHandler($handler)
            ->stackTraceDepth(2)
            ->build();

        $graveyard->tombstone([], StackTraceFixture::getTraceFixture(), []);
    }

    /**
     * @test
     */
    public function build_rootDirSet_logRelativePaths(): void
    {
        $handler = $this->createMock(HandlerInterface::class);
        $handler
            ->expects($this->any())
            ->method('log')
            ->with($this->callback($this->assertRelativeFilePath()));

        $graveyard = $this->builder
            ->withHandler($handler)
            ->rootDir(StackTraceFixture::ROOT_DIR)
            ->build();

        $graveyard->tombstone([], StackTraceFixture::getTraceFixture(), []);
    }

    /**
     * @test
     */
    public function build_buffered_buildBufferedGraveyard(): void
    {
        $graveyard = $this->builder->buffered()->build();

        $this->assertInstanceOf(BufferedGraveyard::class, $graveyard);
    }

    /**
     * @test
     */
    public function build_autoRegister_setToGraveyardRegistry(): void
    {
        $graveyard = $this->builder->autoRegister()->build();

        $this->assertSame($graveyard, GraveyardRegistry::getGraveyard());
    }
}
