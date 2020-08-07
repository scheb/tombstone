<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Source;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractor;
use Scheb\Tombstone\Analyzer\Source\TombstoneVisitor;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneExtractorIntegrationTest extends TestCase
{
    /**
     * @var MockObject|TombstoneIndex
     */
    private $tombstoneIndex;

    /**
     * @var TombstoneExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());
        $traverser = new NodeTraverser();
        $this->extractor = new TombstoneExtractor($parser, $traverser, $this->tombstoneIndex);
        $traverser->addVisitor(new TombstoneVisitor($this->extractor));
    }

    private function createFilePath(string $absoluteFilePath): FilePathInterface
    {
        $filePath = $this->createMock(FilePathInterface::class);
        $filePath
            ->expects($this->any())
            ->method('getAbsolutePath')
            ->willReturn($absoluteFilePath);

        return $filePath;
    }

    private function assertTombstoneParameters(array $arguments): Callback
    {
        return $this->callback(function (Tombstone $tombstone) use ($arguments): bool {
            $this->assertEquals($arguments, $tombstone->getArguments());

            return true;
        });
    }

    private function assertTombstoneLocation(FilePathInterface $file, int $line, ?string $method): Callback
    {
        return $this->callback(function (Tombstone $tombstone) use ($file, $line, $method): bool {
            $this->assertSame($file, $tombstone->getFile());
            $this->assertEquals($line, $tombstone->getLine());
            if (null === $method) {
                $this->assertNull($tombstone->getMethod());
            } else {
                $this->assertEquals($method, $tombstone->getMethod());
            }

            return true;
        });
    }

    /**
     * @test
     */
    public function extractTombstones_parameterTypes_extractTombstonesWithSupportedParameters(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/parameters.php');

        $this->tombstoneIndex
            ->expects($this->exactly(2))
            ->method('addTombstone')
            ->withConsecutive(
                [$this->assertTombstoneParameters(['2020-01-01', 'author', 'label'])],
                [$this->assertTombstoneParameters([null, null, null, null, null, null, 'label'])]
            );

        $this->extractor->extractTombstones($filePath);
    }

    /**
     * @test
     */
    public function extractTombstones_tombstoneLocations_extractFunctionNames(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/location.php');

        $this->tombstoneIndex
            ->expects($this->exactly(5))
            ->method('addTombstone')
            ->withConsecutive(
                [$this->assertTombstoneLocation($filePath, 6, null)],
                [$this->assertTombstoneLocation($filePath, 10, 'globalFunction')],
                [$this->assertTombstoneLocation($filePath, 15, null)],
                [$this->assertTombstoneLocation($filePath, 21, 'Foo\\Bar->method')],
                [$this->assertTombstoneLocation($filePath, 26, 'Foo\\Bar::staticFunction')]
            );

        $this->extractor->extractTombstones($filePath);
    }
}
