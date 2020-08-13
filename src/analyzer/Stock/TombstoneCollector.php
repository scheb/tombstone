<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Stock;

use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;

class TombstoneCollector
{
    /**
     * @var TombstoneProviderInterface[]
     */
    private $tombstoneProviders;

    /**
     * @var TombstoneIndex
     */
    private $tombstoneIndex;

    public function __construct(array $tombstoneProviders, TombstoneIndex $tombstoneIndex)
    {
        $this->tombstoneProviders = $tombstoneProviders;
        $this->tombstoneIndex = $tombstoneIndex;
    }

    public function collectTombstones(): void
    {
        foreach ($this->tombstoneProviders as $tombstoneProvider) {
            foreach ($tombstoneProvider->getTombstones() as $tombstone) {
                $this->tombstoneIndex->addTombstone($tombstone);
            }
        }
    }
}
