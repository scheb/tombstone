<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Analyzer\Matching\MatchingStrategyInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class Analyzer
{
    /**
     * @var MatchingStrategyInterface[]
     */
    private $matchingStrategies;

    /**
     * @param MatchingStrategyInterface[] $matchingStrategies
     */
    public function __construct(array $matchingStrategies)
    {
        $this->matchingStrategies = $matchingStrategies;
    }

    public function getResult(TombstoneIndex $tombstoneIndex, VampireIndex $vampireIndex): AnalyzerResult
    {
        $unmatched = $this->match($tombstoneIndex, $vampireIndex);

        return $this->createResult($tombstoneIndex, $unmatched);
    }

    /**
     * @return Vampire[]
     */
    private function match(TombstoneIndex $tombstoneIndex, VampireIndex $vampireIndex): array
    {
        $unmatched = [];

        /** @var Vampire $vampire */
        foreach ($vampireIndex as $vampire) {
            $relatedTombstone = $this->matchVampireToTombstone($vampire, $tombstoneIndex);
            if ($relatedTombstone) {
                $relatedTombstone->addVampire($vampire);
                $vampire->setTombstone($relatedTombstone);
            } else {
                $unmatched[] = $vampire;
            }
        }

        return $unmatched;
    }

    private function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone
    {
        foreach ($this->matchingStrategies as $strategy) {
            if ($matchingTombstone = $strategy->matchVampireToTombstone($vampire, $tombstoneIndex)) {
                return $matchingTombstone;
            }
        }

        return null;
    }

    /**
     * @param Vampire[]      $unmatchedVampires
     */
    private function createResult(TombstoneIndex $tombstoneIndex, array $unmatchedVampires): AnalyzerResult
    {
        $result = new AnalyzerResult();
        $result->setDeleted($unmatchedVampires);

        foreach ($tombstoneIndex as $tombstone) {
            if ($tombstone->hasVampires()) {
                $result->addUndead($tombstone);
            } else {
                $result->addDead($tombstone);
            }
        }

        return $result;
    }
}
