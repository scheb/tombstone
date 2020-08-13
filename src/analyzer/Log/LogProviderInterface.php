<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Model\Vampire;

interface LogProviderInterface
{
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): self;

    /**
     * @return iterable<int, Vampire>
     */
    public function getVampires(): iterable;
}
