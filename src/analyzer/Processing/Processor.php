<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Processing;

use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Core\Model\Vampire;

class Processor
{
    /**
     * @var VampireMatcher
     */
    private $matcher;

    public function __construct(VampireMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function process(TombstoneIndex $tombstoneIndex, VampireIndex $vampireIndex): AnalyzerResult
    {
        $result = new AnalyzerResult();

        /** @var Vampire $vampire */
        foreach ($vampireIndex as $vampire) {
            $matchingTombstone = $this->matcher->matchVampireToTombstone($vampire, $tombstoneIndex);
            if (null !== $matchingTombstone) {
                $matchingTombstone->addVampire($vampire->withTombstone($matchingTombstone));
            } else {
                $result->addDeleted($vampire);
            }
        }

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
