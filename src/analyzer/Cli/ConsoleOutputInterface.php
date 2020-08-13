<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

interface ConsoleOutputInterface
{
    public function write(string $string): void;

    public function writeln(?string $string = null): void;

    public function debug(string $string): void;

    public function createProgressBar(int $width): ProgressBar;

    public function error(string $message, ?\Throwable $exception = null): void;
}
