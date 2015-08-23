<?php
namespace Scheb\Tombstone\Analyzer\Cli;

use PhpParser\Lexer;
use Scheb\Tombstone\Analyzer\Analyzer;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Log\LogDirectoryScanner;
use Scheb\Tombstone\Analyzer\Log\LogReader;
use Scheb\Tombstone\Analyzer\Report\ConsoleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Source\SourceDirectoryScanner;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorFactory;
use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Analyzer\VampireIndex;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends AbstractCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this
            ->setName('analyze')
            ->addArgument('sourceDir', InputArgument::REQUIRED, 'Path to the PHP source')
            ->addArgument('logDir', InputArgument::REQUIRED, 'Path to the log files')
            ->addOption('report', 'r', InputOption::VALUE_REQUIRED, 'Generate HTML to directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $reportDir = $input->getOption('report');
        $sourceDir = realpath($input->getArgument('sourceDir'));
        if (!$sourceDir) {
            $output->writeln('Argument "sourceDir" is not a valid directory');
            return 1;
        }

        $logDir = realpath($input->getArgument('logDir'));
        if (!$logDir) {
            $output->writeln('Argument "logDir" is not a valid directory');
            return 1;
        }

        $result = $this->createResult($sourceDir, $logDir);
        $report = new ConsoleReportGenerator($output);
        $report->generate($result);

        if ($reportDir) {
            $this->generateHtmlReport($reportDir, $result);
        }

        return 0;
    }

    /**
     * @param string $sourceDir
     * @param string $logDir
     *
     * @return AnalyzerResult
     */
    private function createResult($sourceDir, $logDir) {
        $sourceScanner = new SourceDirectoryScanner(TombstoneExtractorFactory::create(new TombstoneIndex($sourceDir)), $sourceDir);
        $tombstoneIndex = $sourceScanner->getTombstones();

        $logScanner = new LogDirectoryScanner(new LogReader(new VampireIndex()), $logDir);
        $vampireIndex = $logScanner->getVampires();

        $analyzer = new Analyzer();
        return $analyzer->getResult($tombstoneIndex, $vampireIndex);
    }

    /**
     * @param string $reportDir
     * @param AnalyzerResult $result
     */
    protected function generateHtmlReport($reportDir, AnalyzerResult $result)
    {
        $this->output->writeln('Generate HTML report');
        try {
            $report = new HtmlReportGenerator($reportDir);
            $report->generate($result);
        } catch (\Exception $e) {
            $this->output->writeln('Could not generate HTML report: '.$e->getMessage());
        }
    }
}
