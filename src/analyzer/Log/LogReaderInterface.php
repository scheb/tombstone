<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireIndex;

interface LogReaderInterface
{
    public function collectVampires(VampireIndex $vampireIndex): void;
}
