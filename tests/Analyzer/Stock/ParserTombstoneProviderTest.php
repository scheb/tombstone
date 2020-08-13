<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Stock\ParserTombstoneProvider;
use Scheb\Tombstone\Analyzer\Stock\TombstoneExtractor;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;
use SebastianBergmann\FinderFacade\FinderFacade;

class ParserTombstoneProviderTest extends TestCase
{
    /**
     * @var MockObject|TombstoneExtractor
     */
    private $extractor;

    /**
     * @var ParserTombstoneProvider
     */
    private $tombstoneProvider;

    protected function setUp(): void
    {
        $finder = new FinderFacade([__DIR__.'/fixtures']);
        $this->extractor = $this->createMock(TombstoneExtractor::class);
        $consoleOutput = $this->createMock(ConsoleOutputInterface::class);

        $this->tombstoneProvider = new ParserTombstoneProvider($finder, $this->extractor, $consoleOutput);
    }

    /**
     * @test
     */
    public function getTombstones_sourceFilesFound_returnAllTombstones(): void
    {
        $tombstone1 = $this->createMock(Tombstone::class);
        $tombstone2 = $this->createMock(Tombstone::class);
        $tombstone3 = $this->createMock(Tombstone::class);

        $this->extractor
            ->expects($this->any())
            ->method('extractTombstones')
            ->withConsecutive(
                [realpath(__DIR__.'/fixtures/location.php')],
                [realpath(__DIR__.'/fixtures/parameters.php')]
            )
            ->willReturnOnConsecutiveCalls(
                [$tombstone1, $tombstone2],
                [$tombstone3]
            );

        /** @var \Generator $returnValue */
        $traversable = $this->tombstoneProvider->getTombstones();
        $items = iterator_to_array($traversable, false);

        $this->assertCount(3, $items);
        $this->assertSame([$tombstone1, $tombstone2, $tombstone3], $items);
    }
}
