<?php

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\VampireIndex;

interface LogReaderFactoryInterface
{
    public static function create(array $config, VampireIndex $vampireIndex, ConsoleOutput $output): LogReaderInterface;
}
