<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Log;

use PHPUnit\Framework\TestCase;
use Scheb\Tombstone\Analyzer\Log\LogCollector;
use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Core\Model\Vampire;

class LogCollectorTest extends TestCase
{
    private function createReader(array $vampires): LogReaderInterface
    {
        $reader = $this->createMock(LogReaderInterface::class);
        $reader
            ->expects($this->any())
            ->method('iterateVampires')
            ->willReturn(new \ArrayIterator($vampires));

        return $reader;
    }

    /**
     * @test
     */
    public function collectLogs_multipleReaders_addVampiresFromEachReaderToIndex(): void
    {
        $vampire1 = $this->createMock(Vampire::class);
        $vampire2 = $this->createMock(Vampire::class);
        $vampire3 = $this->createMock(Vampire::class);

        $reader1 = $this->createReader([$vampire1, $vampire2]);
        $reader2 = $this->createReader([$vampire3]);
        $vampireIndex = $this->createMock(VampireIndex::class);

        $vampireIndex
            ->expects($this->exactly(3))
            ->method('addVampire')
            ->withConsecutive(
                [$this->identicalTo($vampire1)],
                [$this->identicalTo($vampire2)],
                [$this->identicalTo($vampire3)]
            );

        $collector = new LogCollector([$reader1, $reader2], $vampireIndex);
        $collector->collectLogs();
    }
}
