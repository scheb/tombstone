<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Console;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Core\Model\Tombstone;

class FormattedConsoleOutput
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    public function writeln(string $text): void
    {
        $this->output->writeln($text);
    }

    public function newLine(): void
    {
        $this->writeln('');
    }

    public function printTombstone(Tombstone $tombstone, string $prefix): void
    {
        $this->output->writeln(sprintf('  [%s] <info>%s</info>', $prefix, (string) $tombstone));
        $this->output->writeln(sprintf('    in <comment>line %s</comment>', $tombstone->getLine()));
        if ($tombstone->getMethod()) {
            $this->output->writeln(sprintf('    in method <comment>%s</comment>', $tombstone->getMethod()));
        } else {
            $this->output->writeln(sprintf('    in global scope'));
        }
    }

    /**
     * @psalm-type list<string|null>
     */
    public function printCalledBy(array $invokers): void
    {
        foreach ($invokers as $invoker) {
            $this->output->writeln(sprintf('    was called by <error>%s</error>', $invoker ?: 'global scope'));
        }
    }
}
