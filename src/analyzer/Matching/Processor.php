<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Matching;

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
        $dead = [];
        $undead = [];
        $deleted = [];

        /** @var Vampire $vampire */
        foreach ($vampireIndex as $vampire) {
            $matchingTombstone = $this->matcher->matchVampireToTombstone($vampire, $tombstoneIndex);
            if (null !== $matchingTombstone) {
                $matchingTombstone->addVampire($vampire->withTombstone($matchingTombstone));
            } else {
                $deleted[] = $vampire;
            }
        }

        foreach ($tombstoneIndex as $tombstone) {
            if ($tombstone->hasVampires()) {
                $undead[] = $tombstone;
            } else {
                $dead[] = $tombstone;
            }
        }

        return new AnalyzerResult($dead, $undead, $deleted);
    }
}
