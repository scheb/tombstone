<?php
namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneList;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

interface MatchingStrategyInterface
{
    /**
     * @param Vampire $vampire
     * @param TombstoneList $tombstoneList
     *
     * @return Tombstone|null
     */
    public function matchVampireToTombstone(Vampire $vampire, TombstoneList $tombstoneList);
}
