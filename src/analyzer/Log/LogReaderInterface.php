<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Core\Model\Vampire;

interface LogReaderInterface
{
    /**
     * @return \Traversable<int, Vampire>
     */
    public function iterateVampires(): \Traversable;
}
