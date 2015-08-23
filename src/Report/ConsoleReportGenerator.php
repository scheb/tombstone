<?php
namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Report\Console\FormattedConsoleOutput;
use Scheb\Tombstone\Analyzer\Report\Console\TimePeriodFormatter;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;
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

        $this->output->newLine();
        $this->output->writeln(sprintf('Vampires/Tombstones: %d/%d', $numUndead, $numUndead + $numDead));
        $this->output->writeln(sprintf('Deleted tombstones: %d', $numDeleted));

        foreach ($result->getPerFile() as $file => $fileResult) {
            $this->output->newLine();
            $this->output->writeln($file);
            $this->displayVampires($fileResult['undead']);
            $this->displayTombstones($fileResult['dead']);
            $this->displayDeleted($fileResult['deleted']);
        }
    }

    /**
     * @param Tombstone[] $result
     */
    private function displayVampires($result)
    {
        foreach ($result as $tombstone) {
            $this->output->newLine();
            $this->output->printTombstone($tombstone, 'Vampire');
            $invokers = array();
            foreach ($tombstone->getVampires() as $vampire) {
                $invokers[] = $vampire->getInvoker();
            }
            $this->output->printCalledBy(array_unique($invokers));
        }
    }

    /**
     * @param Tombstone[] $result
     */
    private function displayTombstones($result)
    {
        foreach ($result as $tombstone) {
            $this->output->newLine();
            $this->output->printTombstone($tombstone, 'RIP');
            $date = $tombstone->getTombstoneDate();
            if ($age = TimePeriodFormatter::formatAge($date)) {
                $this->output->writeln(sprintf('  was not called for %s', $age));
            } else {
                $this->output->writeln(sprintf('  was not called since %s', $date));
            }
        }
    }

    /**
     * @param Vampire[] $result
     */
    private function displayDeleted($result) {
        foreach ($result as $vampire) {
            $this->output->newLine();
            $this->output->printTombstone($vampire->getTombstone(), 'Deleted');
        }
    }
}
