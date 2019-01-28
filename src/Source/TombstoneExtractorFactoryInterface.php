<?php

namespace Scheb\Tombstone\Analyzer\Source;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\TombstoneIndex;

interface TombstoneExtractorFactoryInterface
{
    public static function create(array $config, TombstoneIndex $tombstoneIndex, ConsoleOutput $output): TombstoneExtractorInterface;
}
