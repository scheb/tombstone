<?php

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\VampireIndex;

class LogReaderFactory implements LogReaderFactoryInterface
{
    public static function create(array $config, VampireIndex $vampireIndex, ConsoleOutput $output): LogReaderInterface
    {
        return new AnalyzerLogDirectoryScanner(new AnalyzerLogReader($vampireIndex), $config['logs']['directory'], $output);
    }
}
