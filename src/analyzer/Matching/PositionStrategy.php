<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;

class PositionStrategy implements MatchingStrategyInterface
{
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone
    {
        if ($matchingTombstone = $tombstoneIndex->getInFileAndLine($vampire->getFile(), $vampire->getLine())) {
            if ($vampire->inscriptionEquals($matchingTombstone)) {
                return $matchingTombstone;
            }
        }

        return null;
    }
}
