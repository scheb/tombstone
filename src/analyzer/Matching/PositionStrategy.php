<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class PositionStrategy implements MatchingStrategyInterface
{
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone
    {
        if ($matchingTombstone = $tombstoneIndex->getInFileAndLine($vampire->getFile()->getReferencePath(), $vampire->getLine())) {
            if ($vampire->inscriptionEquals($matchingTombstone)) {
                return $matchingTombstone;
            }
        }

        return null;
    }
}
