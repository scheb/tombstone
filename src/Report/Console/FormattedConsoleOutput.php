<?php
namespace Scheb\Tombstone\Analyzer\Report\Console;

use Scheb\Tombstone\Tombstone;
use Symfony\Component\Console\Output\OutputInterface;

class FormattedConsoleOutput
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param $text
     */
    public function writeln($text)
    {
        $this->output->writeln($text);
    }

    public function newLine()
    {
        $this->writeln('');
    }

    /**
     * @param Tombstone $tombstone
     */
    public function printTombstone($tombstone, $prefix)
    {
        $this->output->writeln(sprintf('  [%s] <info>%s</info>', $prefix, (string) $tombstone));
        $this->output->writeln(sprintf('    in <comment>line %s</comment>', $tombstone->getLine()));
        if ($tombstone->getMethod()) {
            $this->output->writeln(sprintf('    in method <comment>%s</comment>', $tombstone->getMethod()));
        } else {
            $this->output->writeln(sprintf('  in global scope'));
        }
    }

    /**
     * @param string[] $invokers
     */
    public function printCalledBy(array $invokers) {
        foreach ($invokers as $invoker) {
            $this->output->writeln(sprintf('    was called by <error>%s</error>', $invoker ?: 'global scope'));
        }
    }
}
