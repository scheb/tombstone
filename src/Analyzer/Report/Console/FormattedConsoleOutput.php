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
     * @param string $text
     */
    public function headline($text)
    {
        $this->output->writeln($text);
        $this->output->writeln(str_repeat('-', strlen($text)));
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
    public function printTombstone($tombstone)
    {
        $this->output->writeln(sprintf('<info>tombstone("%s", "%s")</info>', $tombstone->getTombstoneDate(), $tombstone->getAuthor()));
        $this->output->writeln(sprintf('  in file <comment>%s:%s</comment>', $tombstone->getFile(), $tombstone->getLine()));
        $this->output->writeln(sprintf('  in method <comment>%s</comment>', $tombstone->getMethod()));
    }

    /**
     * @param string[] $invokers
     */
    public function printCalledBy(array $invokers) {
        foreach ($invokers as $invoker) {
            $this->output->writeln(sprintf('  was called by <error>%s</error>', $invoker ?: 'global scope'));
        }
    }
}
