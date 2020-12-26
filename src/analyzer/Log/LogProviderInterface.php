<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Model\Vampire;

interface LogProviderInterface
{
    /**
     * @param array $config All config options from the YAML file. Additional config options are passed through.
     * @param ConsoleOutputInterface $consoleOutput can be used to write output to the console
     */
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): self;

    /**
     * Must return an iterable (array or \Traversable) of Vampire objects.
     *
     * @return iterable<int, Vampire>
     */
    public function getVampires(): iterable;
}
