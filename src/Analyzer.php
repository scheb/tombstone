<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Analyzer\Matching\MatchingStrategyInterface;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class Analyzer
{
    /**
     * @var MatchingStrategyInterface[]
     */
    private $matchingStrategies;

    public function __construct()
    {
        $this->matchingStrategies[] = new MethodNameStrategy();
        $this->matchingStrategies[] = new PositionStrategy();
    }

    /**
     * @param TombstoneIndex $tombstoneIndex
     * @param VampireIndex $vampireIndex
     *
     * @return AnalyzerResult
     */
    public function getResult(TombstoneIndex $tombstoneIndex, VampireIndex $vampireIndex)
    {
        $unmatched = $this->match($tombstoneIndex, $vampireIndex);
        return $this->createResult($tombstoneIndex, $unmatched);
    }

    /**
     * @param TombstoneIndex $tombstoneIndex
     * @param VampireIndex $vampireIndex
     *
     * @return Vampire[]
     */
    private function match(TombstoneIndex $tombstoneIndex, VampireIndex $vampireIndex)
    {
        $unmatched = array();

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

    /**
     * @param Vampire $vampire
     * @param TombstoneIndex $tombstoneIndex
     *
     * @return Tombstone|null
     */
    private function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex)
    {
        foreach ($this->matchingStrategies as $strategy) {
            if ($matchingTombstone = $strategy->matchVampireToTombstone($vampire, $tombstoneIndex)) {
                return $matchingTombstone;
            }
        }

        return null;
    }

    /**
     * @param TombstoneIndex $tombstoneIndex
     * @param array $unmatchedVampires
     *
     * @return AnalyzerResult
     */
    private function createResult(TombstoneIndex $tombstoneIndex, array $unmatchedVampires)
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
