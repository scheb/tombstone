<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Stock;

use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Stock\TombstoneCollector;
use Scheb\Tombstone\Analyzer\Stock\TombstoneProviderInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneCollectorTest extends TestCase
{
    private function createProvider(array $tombstones): TombstoneProviderInterface
    {
        $provider = $this->createMock(TombstoneProviderInterface::class);
        $provider
            ->expects($this->any())
            ->method('getTombstones')
            ->willReturn(new \ArrayIterator($tombstones));

        return $provider;
    }

    /**
     * @test
     */
    public function collectTombstones_multipleProviders_addVampiresFromEachProviderToIndex(): void
    {
        $tombstone1 = $this->createMock(Tombstone::class);
        $tombstone2 = $this->createMock(Tombstone::class);
        $tombstone3 = $this->createMock(Tombstone::class);

        $provider1 = $this->createProvider([$tombstone1, $tombstone2]);
        $provider2 = $this->createProvider([$tombstone3]);
        $tombstoneIndex = $this->createMock(TombstoneIndex::class);

        $tombstoneIndex
            ->expects($this->exactly(3))
            ->method('addTombstone')
            ->withConsecutive(
                [$this->identicalTo($tombstone1)],
                [$this->identicalTo($tombstone2)],
                [$this->identicalTo($tombstone3)]
            );

        $collector = new TombstoneCollector([$provider1, $provider2], $tombstoneIndex);
        $collector->collectTombstones();
    }
}
