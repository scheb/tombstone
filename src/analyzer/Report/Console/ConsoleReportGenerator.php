<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Console;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Analyzer\Report\TimePeriodFormatter;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class ConsoleReportGenerator implements ReportGeneratorInterface
{
    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var int
     */
    private $now;

    public function __construct(ConsoleOutputInterface $output)
    {
        $this->output = $output;
        $this->now = time();
    }

    public function getName(): string
    {
        return 'Console';
    }

    public function generate(AnalyzerResult $result): void
    {
        $numUndead = $result->getUndeadCount();
        $numDead = $result->getDeadCount();
        $numDeleted = $result->getDeletedCount();

        $this->newLine();
        $this->output->writeln(sprintf('Vampires/Tombstones: %d/%d', $numUndead, $numUndead + $numDead));
        $this->output->writeln(sprintf('Deleted tombstones: %d', $numDeleted));

        foreach ($result->getFileResults() as $fileResult) {
            $this->newLine();

            $this->output->writeln($fileResult->getFile()->getReferencePath());
            $this->displayVampires($fileResult->getUndead());
            $this->displayTombstones($fileResult->getDead());
            $this->displayDeleted($fileResult->getDeleted());
        }
    }

    /**
     * @param Tombstone[] $result
     */
    private function displayVampires(array $result): void
    {
        foreach ($result as $tombstone) {
            $this->newLine();
            $this->printTombstone($tombstone, 'Vampire');
            $invokers = [];
            foreach ($tombstone->getVampires() as $vampire) {
                $invokers[] = $vampire->getInvoker();
            }
            $this->printCalledBy(array_unique($invokers));
        }
    }

    /**
     * @psalm-type list<string|null>
     */
    private function printCalledBy(array $invokers): void
    {
        foreach ($invokers as $invoker) {
            $this->output->writeln(sprintf('    was called by <error>%s</error>', $invoker ?: 'global scope'));
        }
    }

    /**
     * @param Tombstone[] $result
     */
    private function displayTombstones(array $result): void
    {
        foreach ($result as $tombstone) {
            $this->newLine();
            $this->printTombstone($tombstone, 'RIP');
            $date = $tombstone->getTombstoneDate();
            if ($date) {
                if ($age = TimePeriodFormatter::formatAge($date)) {
                    $this->output->writeln(sprintf('    was not called for %s', $age));
                } else {
                    $this->output->writeln(sprintf('    was not called since %s', $date));
                }
            }
        }
    }

    private function printTombstone(Tombstone $tombstone, string $prefix): void
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
     * @param Vampire[] $result
     */
    private function displayDeleted(array $result): void
    {
        foreach ($result as $vampire) {
            $this->newLine();
            $this->printTombstone($vampire->getTombstone(), 'Deleted');
        }
    }

    private function newLine(): void
    {
        $this->output->writeln('');
    }
}
