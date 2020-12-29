<?php

declare(strict_types=1);

namespace Scheb\Tombstone\TestApplication\Tombstone;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Log\LogProviderInterface;

class LogProvider implements LogProviderInterface
{
    public function __construct(array $config, ConsoleOutputInterface $consoleOutput, bool $isCalled)
    {
    }

    public static function create(array $config, ConsoleOutputInterface $consoleOutput): LogProviderInterface
    {
        // Ensure the class was instantiated through the create method
        return new self($config, $consoleOutput, true);
    }

    public function getVampires(): iterable
    {
        return [];
    }
}
