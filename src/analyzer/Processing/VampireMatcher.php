<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Processing;

use Scheb\Tombstone\Analyzer\Matching\MatchingStrategyInterface;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class VampireMatcher
{
    /**
     * @var MatchingStrategyInterface[]
     */
    private $matchingStrategies;

    public function __construct(array $matchingStrategies)
    {
        $this->matchingStrategies = $matchingStrategies;
    }

    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone
    {
        foreach ($this->matchingStrategies as $strategy) {
            if ($matchingTombstone = $strategy->matchVampireToTombstone($vampire, $tombstoneIndex)) {
                return $matchingTombstone;
            }
        }

        return null;
    }
}
