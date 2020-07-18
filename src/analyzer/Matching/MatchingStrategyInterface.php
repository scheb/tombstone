<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;

interface MatchingStrategyInterface
{
    public function matchVampireToTombstone(Vampire $vampire, TombstoneIndex $tombstoneIndex): ?Tombstone;
}
