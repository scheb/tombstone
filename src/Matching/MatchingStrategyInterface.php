<?php

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

interface MatchingStrategyInterface
{
    /**
     * @param Vampire        $vampire
     * @param TombstoneIndex $tombstoneIndex
     *
     * @return Tombstone|null
     */
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex);
}
