<?php

declare(strict_types=1);

namespace Scheb\Tombstone\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Model\Vampire;
use Scheb\Tombstone\Tracing\TraceProvider;

class LogProvider implements LogReaderInterface
{
    public function collectVampires(VampireIndex $vampireIndex): void
    {
        $trace = TraceProvider::getTraceHere(0);
        $vampire = Vampire::createFromCall(['customProvided'], $trace, []);
        $vampireIndex->addVampire($vampire);
    }
}
