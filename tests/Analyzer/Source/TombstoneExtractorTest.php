<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Source;

use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractionException;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractor;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

class TombstoneExtractorTest extends TestCase
{
    /**
     * @var MockObject|Parser
     */
    private $parser;

    /**
     * @var MockObject|NodeTraverserInterface
     */
    private $traverser;

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
        $this->parser = $this->createMock(Parser::class);
        $this->traverser = $this->createMock(NodeTraverserInterface::class);
        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);

        $this->extractor = new TombstoneExtractor($this->parser, $this->traverser, $this->tombstoneIndex);
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

    /**
     * @test
     */
    public function extractTombstones_tombstoneFound_addToTombstoneIndex(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/parameters.php');
        $statements = [$this->createMock(Stmt::class)];
        $this->parser
            ->expects($this->once())
            ->method('parse')
            ->with(file_get_contents(__DIR__.'/fixtures/parameters.php'))
            ->willReturn($statements);

        $this->traverser
            ->expects($this->once())
            ->method('traverse')
            ->with($this->identicalTo($statements))
            ->willReturnCallback(function (array $statements): array {
                $this->extractor->onTombstoneFound(['args'], 123, 'method');

                return $statements;
            });

        $this->tombstoneIndex
            ->expects($this->once())
            ->method('addTombstone')
            ->with($this->callback(function (Tombstone $tombstone) use ($filePath): bool {
                $this->assertEquals(['args'], $tombstone->getArguments());
                $this->assertSame($filePath, $tombstone->getFile());
                $this->assertEquals(123, $tombstone->getLine());
                $this->assertEquals('method', $tombstone->getMethod());

                return true;
            }));

        $this->extractor->extractTombstones($filePath);
    }

    /**
     * @test
     */
    public function extractTombstones_fileNotReadable_throwTombstoneExtractionException(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/does_not_exist');

        $this->expectException(TombstoneExtractionException::class);
        $this->expectExceptionMessage('is not readable');

        $this->extractor->extractTombstones($filePath);
    }

    /**
     * @test
     */
    public function extractTombstones_parserReturnsNull_throwTombstoneExtractionException(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/parameters.php');
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willReturn(null);

        $this->expectException(TombstoneExtractionException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones($filePath);
    }

    /**
     * @test
     */
    public function extractTombstones_parserThrowsError_throwTombstoneExtractionException(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/parameters.php');
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willThrowException(new Error('msg'));

        $this->expectException(TombstoneExtractionException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones($filePath);
    }

    /**
     * @test
     */
    public function extractTombstones_traverserThrowsError_throwTombstoneExtractionException(): void
    {
        $filePath = $this->createFilePath(__DIR__.'/fixtures/parameters.php');
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willReturn([$this->createMock(Stmt::class)]);

        $this->traverser
            ->expects($this->any())
            ->method('traverse')
            ->willThrowException(new Error('msg'));

        $this->expectException(TombstoneExtractionException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones($filePath);
    }
}
