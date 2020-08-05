<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report\Console;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class ConsoleReportGenerator implements ReportGeneratorInterface
{
    /**
     * @var FormattedConsoleOutput
     */
    private $output;

    /**
     * @var int
     */
    private $now;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = new FormattedConsoleOutput($output);
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

        $this->output->newLine();
        $this->output->writeln(sprintf('Vampires/Tombstones: %d/%d', $numUndead, $numUndead + $numDead));
        $this->output->writeln(sprintf('Deleted tombstones: %d', $numDeleted));

        foreach ($result->getFileResults() as $fileResult) {
            $this->output->newLine();

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
            $this->output->newLine();
            $this->output->printTombstone($tombstone, 'Vampire');
            $invokers = [];
            foreach ($tombstone->getVampires() as $vampire) {
                $invokers[] = $vampire->getInvoker();
            }
            $this->output->printCalledBy(array_unique($invokers));
        }
    }

    /**
     * @param Tombstone[] $result
     */
    private function displayTombstones(array $result): void
    {
        foreach ($result as $tombstone) {
            $this->output->newLine();
            $this->output->printTombstone($tombstone, 'RIP');
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

    /**
     * @param Vampire[] $result
     */
    private function displayDeleted(array $result): void
    {
        foreach ($result as $vampire) {
            $this->output->newLine();
            $this->output->printTombstone($vampire->getTombstone(), 'Deleted');
        }
    }
}
