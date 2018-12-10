<?php

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class MethodNameStrategy implements MatchingStrategyInterface
{
    /**
     * @param Vampire        $vampire
     * @param TombstoneIndex $tombstoneIndex
     *
     * @return Tombstone|null
     */
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex)
    {
        if ($matchingTombstones = $tombstoneIndex->getInMethod($vampire->getMethod())) {
            foreach ($matchingTombstones as $matchingTombstone) {
                if ($vampire->inscriptionEquals($matchingTombstone)) {
                    return $matchingTombstone;
                }
            }
        }

        return null;
    }
}
