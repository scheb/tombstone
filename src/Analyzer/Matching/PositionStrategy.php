<?php
namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneList;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class PositionStrategy implements MatchingStrategyInterface
{
    /**
     * @param Vampire $vampire
     * @param TombstoneList $tombstoneList
     * @return Tombstone|null
     */
    public function matchVampireToTombstone(Vampire $vampire, TombstoneList $tombstoneList)
    {
        if ($matchingTombstone = $tombstoneList->getInFileAndLine($vampire->getFile(), $vampire->getLine())) {
            if ($matchingTombstone->getAuthor() === $vampire->getAuthor() && $matchingTombstone->getTombstoneDate() === $vampire->getTombstoneDate()) {
                return $matchingTombstone;
            }
        }

        return null;
    }
}
