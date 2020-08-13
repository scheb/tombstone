<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Stock;

use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Stock\TombstoneExtractor;
use Scheb\Tombstone\Analyzer\Stock\TombstoneExtractorException;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneExtractorTest extends TestCase
{
    /**
     * @var MockObject|FilePathInterface
     */
    private $filePath;

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
        $this->filePath = $this->createMock(FilePathInterface::class);
        $sourceRootPath = $this->createMock(RootPath::class);
        $sourceRootPath
            ->expects($this->any())
            ->method('createFilePath')
            ->willReturn($this->filePath);

        $this->parser = $this->createMock(Parser::class);
        $this->traverser = $this->createMock(NodeTraverserInterface::class);
        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);

        $this->extractor = new TombstoneExtractor($this->parser, $this->traverser, $sourceRootPath);
    }

    /**
     * @test
     */
    public function extractTombstones_tombstoneFound_addToTombstoneIndex(): void
    {
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
                $this->extractor->onTombstoneFound('tombstone', ['args'], 123, 'method');

                return $statements;
            });

        $returnValue = $this->extractor->extractTombstones(__DIR__.'/fixtures/parameters.php');

        $this->assertCount(1, $returnValue);
        $tombstone = $returnValue[0];
        $this->assertEquals(['args'], $tombstone->getArguments());
        $this->assertSame($this->filePath, $tombstone->getFile());
        $this->assertEquals(123, $tombstone->getLine());
        $this->assertEquals('method', $tombstone->getMethod());
    }

    /**
     * @test
     */
    public function extractTombstones_fileNotReadable_throwTombstoneExtractionException(): void
    {
        $this->expectException(TombstoneExtractorException::class);
        $this->expectExceptionMessage('is not readable');

        $this->extractor->extractTombstones(__DIR__.'/does_not_exist');
    }

    /**
     * @test
     */
    public function extractTombstones_parserReturnsNull_throwTombstoneExtractionException(): void
    {
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willReturn(null);

        $this->expectException(TombstoneExtractorException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones(__DIR__.'/fixtures/parameters.php');
    }

    /**
     * @test
     */
    public function extractTombstones_parserThrowsError_throwTombstoneExtractionException(): void
    {
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willThrowException(new Error('msg'));

        $this->expectException(TombstoneExtractorException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones(__DIR__.'/fixtures/parameters.php');
    }

    /**
     * @test
     */
    public function extractTombstones_traverserThrowsError_throwTombstoneExtractionException(): void
    {
        $this->parser
            ->expects($this->any())
            ->method('parse')
            ->willReturn([$this->createMock(Stmt::class)]);

        $this->traverser
            ->expects($this->any())
            ->method('traverse')
            ->willThrowException(new Error('msg'));

        $this->expectException(TombstoneExtractorException::class);
        $this->expectExceptionMessage('could not be parsed');

        $this->extractor->extractTombstones(__DIR__.'/fixtures/parameters.php');
    }
}
