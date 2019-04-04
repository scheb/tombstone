<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class MethodNameStrategy implements MatchingStrategyInterface
{
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone
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
