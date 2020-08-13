<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Stock;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

interface TombstoneProviderInterface
{
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): self;

    /**
     * @return iterable<int, Tombstone>
     */
    public function getTombstones(): iterable;
}
