<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Analyzer\VampireIndex;
use Scheb\Tombstone\Tracing\TraceProvider;
use Scheb\Tombstone\Vampire;

class LogProvider implements LogReaderInterface
{
    public function collectVampires(VampireIndex $vampireIndex): void
    {
        $trace = TraceProvider::getTraceHere(0);
        $vampire = Vampire::createFromCall(['customProvided'], $trace, []);
        $vampireIndex->addVampire($vampire);
    }
}
