<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Stock;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Stock\TombstoneExtractor;
use Scheb\Tombstone\Analyzer\Stock\TombstoneNodeVisitor;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneExtractorIntegrationTest extends TestCase
{
    /**
     * @var MockObject|FilePathInterface
     */
    private $filePath;

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

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());
        $traverser = new NodeTraverser();
        $this->extractor = new TombstoneExtractor($parser, $traverser, $sourceRootPath);
        $traverser->addVisitor(new TombstoneNodeVisitor($this->extractor));
    }

    private function assertTombstoneLocation(int $line, ?string $method, Tombstone $tombstone): void
    {
        $this->assertSame($this->filePath, $tombstone->getFile());

        $this->assertEquals($line, $tombstone->getLine());
        if (null === $method) {
            $this->assertNull($tombstone->getMethod());
        } else {
            $this->assertEquals($method, $tombstone->getMethod());
        }
    }

    /**
     * @test
     */
    public function extractTombstones_parameterTypes_extractTombstonesWithSupportedParameters(): void
    {
        $returnValue = $this->extractor->extractTombstones(__DIR__.'/fixtures/parameters.php');
        $this->assertCount(2, $returnValue);
        $this->assertEquals(['2020-01-01', 'author', 'label'], $returnValue[0]->getArguments());
        $this->assertEquals([null, null, null, null, null, null, 'label'], $returnValue[1]->getArguments());
    }

    /**
     * @test
     */
    public function extractTombstones_tombstoneLocations_extractTombstonesWithLocation(): void
    {
        $returnValue = $this->extractor->extractTombstones(__DIR__.'/fixtures/location.php');
        $this->assertCount(5, $returnValue);
        $this->assertTombstoneLocation(6, null, $returnValue[0]);
        $this->assertTombstoneLocation(10, 'globalFunction', $returnValue[1]);
        $this->assertTombstoneLocation(15, null, $returnValue[2]);
        $this->assertTombstoneLocation(21, 'Foo\\Bar->method', $returnValue[3]);
        $this->assertTombstoneLocation(26, 'Foo\\Bar::staticFunction', $returnValue[4]);
    }
}
