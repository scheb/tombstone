<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;

interface TombstoneExtractorFactoryInterface
{
    public static function create(array $config, TombstoneIndex $tombstoneIndex, ConsoleOutput $output): TombstoneExtractorInterface;
}
