<?php

namespace Scheb\Tombstone\Analyzer\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Analyzer\VampireIndex;

class LogProvider implements LogReaderInterface
{
    public function collectVampires(VampireIndex $vampireIndex): void
    {
    }
}
