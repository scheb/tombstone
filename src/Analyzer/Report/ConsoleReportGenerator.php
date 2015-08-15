<?php
namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Report\Console\FormattedConsoleOutput;
use Scheb\Tombstone\Analyzer\Report\Console\TimePeriodFormatter;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleReportGenerator implements ReportGeneratorInterface
{
    /**
     * @var FormattedConsoleOutput
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = new FormattedConsoleOutput($output);
        $this->now = time();
    }

    public function generate(AnalyzerResult $result)
    {
        $numUndead = count($result->getUndead());
        $numDead = count($result->getDead());
        $numDeleted = count($result->getDeleted());

        if ($numUndead) {
            $this->output->headline(sprintf('Vampires (%d)', $numUndead));
            $this->output->writeln('Those tombstones have been called according to the logs.');
            $this->output->newLine();
            $this->displayVampires($result);
        }

        if ($numDead) {
            $this->output->headline(sprintf('Tombstones (%d)', $numDead));
            $this->output->writeln('Those tombstones have not been called. Consider deleting the dead code.');
            $this->output->newLine();
            $this->displayTombstones($result);
        }

        if ($numDeleted) {
            $this->output->headline(sprintf('Deleted tombstones (%d)', $numDeleted));
            $this->output->writeln('Those appear in the log but could not be found in the code. They\'re assumed to be deleted.');
            $this->output->newLine();
            $this->displayDeleted($result);
        }
    }

    /**
     * @param AnalyzerResult $result
     */
    private function displayVampires(AnalyzerResult $result)
    {
        foreach ($result->getUndead() as $tombstone) {
            $this->output->printTombstone($tombstone);
            $invokers = array();
            foreach ($tombstone->getVampires() as $vampire) {
                $invokers[] = $vampire->getInvoker();
            }
            $this->output->printCalledBy(array_unique($invokers));
            $this->output->newLine();
        }
    }

    /**
     * @param AnalyzerResult $result
     */
    private function displayTombstones(AnalyzerResult $result)
    {
        foreach ($result->getDead() as $tombstone) {
            $this->output->printTombstone($tombstone);
            $date = $tombstone->getTombstoneDate();
            if ($age = TimePeriodFormatter::formatAge($date)) {
                $this->output->writeln(sprintf('  was not called for %s', $age));
            } else {
                $this->output->writeln(sprintf('  was not called since %s', $date));
            }
            $this->output->newLine();
        }
    }

    /**
     * @param AnalyzerResult $result
     */
    private function displayDeleted(AnalyzerResult $result) {
        foreach ($result->getDeleted() as $vampire) {
            $this->output->printTombstone($vampire->getTombstone());
        }
    }
}
