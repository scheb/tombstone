<?php

declare(strict_types=1);

namespace Scheb\Tombstone\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Log\LogProviderInterface;

class LogProvider implements LogProviderInterface
{
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): LogProviderInterface
    {
        return new self();
    }

    public function getVampires(): iterable
    {
        return [];
    }
}
