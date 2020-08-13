<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Log\LogCollector;
use Scheb\Tombstone\Analyzer\Log\LogProviderInterface;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class LogCollectorTest extends TestCase
{
    private function createProvider(array $vampires): LogProviderInterface
    {
        $provider = $this->createMock(LogProviderInterface::class);
        $provider
            ->expects($this->any())
            ->method('getVampires')
            ->willReturn(new \ArrayIterator($vampires));

        return $provider;
    }

    /**
     * @test
     */
    public function collectLogs_multipleProviders_addVampiresFromEachProviderToIndex(): void
    {
        $vampire1 = $this->createMock(Vampire::class);
        $vampire2 = $this->createMock(Vampire::class);
        $vampire3 = $this->createMock(Vampire::class);

        $provider1 = $this->createProvider([$vampire1, $vampire2]);
        $provider2 = $this->createProvider([$vampire3]);
        $vampireIndex = $this->createMock(VampireIndex::class);

        $vampireIndex
            ->expects($this->exactly(3))
            ->method('addVampire')
            ->withConsecutive(
                [$this->identicalTo($vampire1)],
                [$this->identicalTo($vampire2)],
                [$this->identicalTo($vampire3)]
            );

        $collector = new LogCollector([$provider1, $provider2], $vampireIndex);
        $collector->collectLogs();
    }
}
