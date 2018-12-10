<?php

namespace Scheb\Tombstone\Analyzer\Cli;

use Scheb\Tombstone\Analyzer\Analyzer;
use Scheb\Tombstone\Analyzer\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Log\LogDirectoryScanner;
use Scheb\Tombstone\Analyzer\Log\LogReader;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Analyzer\Report\ConsoleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Source\SourceDirectoryScanner;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorFactory;
use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Analyzer\VampireIndex;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Helper\ProgressBar;
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
            ->setName('tombstone')
            ->addArgument('source-dir', InputArgument::REQUIRED, 'Path to PHP source files')
            ->addArgument('log-dir', InputArgument::REQUIRED, 'Path to the log files')
            ->addOption('report-html', 'rh', InputOption::VALUE_REQUIRED, 'Generate HTML report to a directory')
            ->addOption('source-match', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Match source files with these patterns (multiple possible)', ['*.php']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $htmlReportDir = $input->getOption('report-html');
        $sourceDir = realpath($input->getArgument('source-dir'));
        if (!$sourceDir) {
            $output->writeln('Argument "source-dir" is not a valid directory');

            return 1;
        }

        $logDir = realpath($input->getArgument('log-dir'));
        if (!$logDir) {
            $output->writeln('Argument "log-dir" is not a valid directory');

            return 1;
        }

        $result = $this->createResult($sourceDir, $logDir, $input->getOption('source-match'));
        $report = new ConsoleReportGenerator($output, $sourceDir);
        $report->generate($result);

        if ($htmlReportDir) {
            $this->generateHtmlReport($htmlReportDir, $sourceDir, $result);
        }

        return 0;
    }

    /**
     * @param string $sourceDir
     * @param string $logDir
     *
     * @return AnalyzerResult
     */
    private function createResult($sourceDir, $logDir, $regexExpressions)
    {
        $this->output->writeln('');
        $this->output->writeln('Scan source code ...');
        $sourceScanner = new SourceDirectoryScanner(
            TombstoneExtractorFactory::create(
                new TombstoneIndex($sourceDir)
            ),
            $sourceDir,
            $regexExpressions
        );

        $progress = $this->createProgressBar($sourceScanner->getNumFiles());
        $tombstoneIndex = $sourceScanner->getTombstones(function () use ($progress) {
            $progress->advance();
        });

        $this->output->writeln('');
        $this->output->writeln('');

        $this->output->writeln('Read log files ...');
        $logScanner = new LogDirectoryScanner(new LogReader(new VampireIndex()), $logDir);

        $progress = $this->createProgressBar($logScanner->getNumFiles());
        $vampireIndex = $logScanner->getVampires(function () use ($progress) {
            $progress->advance();
        });
        $this->output->writeln('');

        $analyzer = new Analyzer([
            new MethodNameStrategy(),
            new PositionStrategy(),
        ]);

        return $analyzer->getResult($tombstoneIndex, $vampireIndex);
    }

    /**
     * @param string         $reportDir
     * @param string         $sourceDir
     * @param AnalyzerResult $result
     */
    protected function generateHtmlReport($reportDir, $sourceDir, AnalyzerResult $result)
    {
        $this->output->writeln('');
        $this->output->write('Generate HTML report... ');
        try {
            $report = new HtmlReportGenerator($reportDir, $sourceDir);
            $report->generate($result);
            $this->output->writeln('Done');
        } catch (\Exception $e) {
            $this->output->writeln('Failed');
            $this->output->writeln('Could not generate HTML report: '.$e->getMessage());
        }
    }

    /**
     * @param int $width
     *
     * @return ProgressBar
     */
    private function createProgressBar($width)
    {
        $progress = new ProgressBar($this->output, $width);
        $progress->setBarWidth(50);
        $progress->display();

        return $progress;
    }
}
