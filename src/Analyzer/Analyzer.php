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
     * @param TombstoneList $tombstoneList
     * @param VampireList $vampireList
     *
     * @return AnalyzerResult
     */
    public function getResult(TombstoneList $tombstoneList, VampireList $vampireList)
    {
        $unmatched = $this->match($tombstoneList, $vampireList);
        return $this->createResult($tombstoneList, $unmatched);
    }

    /**
     * @param TombstoneList $tombstoneList
     * @param VampireList $vampireList
     *
     * @return Vampire[]
     */
    private function match(TombstoneList $tombstoneList, VampireList $vampireList)
    {
        $unmatched = array();

        /** @var Vampire $vampire */
        foreach ($vampireList as $vampire) {
            $relatedTombstone = $this->matchVampireToTombstone($vampire, $tombstoneList);
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
     * @param TombstoneList $tombstoneList
     *
     * @return Tombstone|null
     */
    private function matchVampireToTombstone(Vampire $vampire, TombstoneList $tombstoneList)
    {
        foreach ($this->matchingStrategies as $strategy) {
            if ($matchingTombstone = $strategy->matchVampireToTombstone($vampire, $tombstoneList)) {
                return $matchingTombstone;
            }
        }

        return null;
    }

    /**
     * @param TombstoneList $tombstoneList
     * @param array $unmatchedVampires
     *
     * @return AnalyzerResult
     */
    private function createResult(TombstoneList $tombstoneList, array $unmatchedVampires)
    {
        $result = new AnalyzerResult();
        $result->setDeleted($unmatchedVampires);

        foreach ($tombstoneList as $tombstone) {
            if ($tombstone->hasVampires()) {
                $result->addDead($tombstone);
            } else {
                $result->addUndead($tombstone);
            }
        }

        return $result;
    }
}
