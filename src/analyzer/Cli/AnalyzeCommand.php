<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

use Scheb\Tombstone\Analyzer\Config\Configuration;
use Scheb\Tombstone\Analyzer\Config\ConfigurationLoader;
use Scheb\Tombstone\Analyzer\Config\YamlConfigProvider;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogProvider;
use Scheb\Tombstone\Analyzer\Log\LogCollector;
use Scheb\Tombstone\Analyzer\Log\LogProviderInterface;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Analyzer\Matching\Processor;
use Scheb\Tombstone\Analyzer\Matching\VampireMatcher;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Analyzer\Report\Checkstyle\CheckstyleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Console\ConsoleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Html\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Php\PhpReportGenerator;
use Scheb\Tombstone\Analyzer\Report\ReportExporter;
use Scheb\Tombstone\Analyzer\Stock\ParserTombstoneProvider;
use Scheb\Tombstone\Analyzer\Stock\TombstoneCollector;
use Symfony\Component\Config\Definition\Processor as ConfigurationProcessor;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends AbstractCommand
{
    /**
     * @var InputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $input;

    /**
     * @var ConsoleOutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $output;

    protected function configure(): void
    {
        $this
            ->setName('analyze')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = new ConsoleOutput($output);

        try {
            $this->doExecute();
        } catch (\Throwable $e) {
            $this->output->writeln($e->getMessage());
            $this->output->debug($e->getTraceAsString());

            return 1;
        }

        return 0;
    }

    private function doExecute(): void
    {
        /** @var string $configFile */
        $configFile = $this->input->getOption('config') ?? getcwd().DIRECTORY_SEPARATOR.'tombstone.yml';
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException(sprintf('Could not find configuration file %s', $configFile));
        }

        $this->output->debug('Load config from '.$configFile);
        $configLoader = new ConfigurationLoader(new ConfigurationProcessor(), new Configuration());
        $config = $configLoader->loadConfiguration([new YamlConfigProvider($configFile)]);

        $tombstoneIndex = new TombstoneIndex();
        $vampireIndex = new VampireIndex();

        $this->createTombstoneCollector($config, $tombstoneIndex)->collectTombstones();
        $this->createLogCollector($config, $vampireIndex)->collectLogs();

        $this->output->writeln('Analyze tombstones ...');
        $result = $this->createAnalyzer()->process($tombstoneIndex, $vampireIndex);

        $this->createReportExporter($config)->generate($result);
    }

    private function createTombstoneCollector(array $config, TombstoneIndex $tombstoneIndex): TombstoneCollector
    {
        $tombstoneProviders = [];
        if (isset($config['tombstones']['parser'])) {
            $tombstoneProviders[] = ParserTombstoneProvider::create($config, $this->output);
        }

        return new TombstoneCollector($tombstoneProviders, $tombstoneIndex);
    }

    private function createLogCollector(array $config, VampireIndex $vampireIndex): LogCollector
    {
        $logProviders = [];
        if (isset($config['logs']['directory'])) {
            $logProviders[] = AnalyzerLogProvider::create($config, $this->output);
        }
        if (isset($config['logs']['custom'])) {
            if (isset($config['logs']['custom']['file'])) {
                /** @psalm-suppress UnresolvableInclude */
                require_once $config['logs']['custom']['file'];
            }

            $reflectionClass = new \ReflectionClass($config['logs']['custom']['class']);
            if (!$reflectionClass->implementsInterface(LogProviderInterface::class)) {
                throw new \Exception(sprintf('Class %s must implement %s', $config['logs']['custom']['class'], LogProviderInterface::class));
            }

            /** @var LogProviderInterface $logReader */
            $logReader = $reflectionClass->newInstance();
            $logProviders[] = $logReader;
        }

        return new LogCollector($logProviders, $vampireIndex);
    }

    private function createAnalyzer(): Processor
    {
        return new Processor(new VampireMatcher([
            new MethodNameStrategy(),
            new PositionStrategy(),
        ]));
    }

    private function createReportExporter(array $config): ReportExporter
    {
        $reportGenerators = [];
        if (isset($config['report']['console'])) {
            $reportGenerators[] = ConsoleReportGenerator::create($config, $this->output);
        }
        if (isset($config['report']['html'])) {
            $reportGenerators[] = HtmlReportGenerator::create($config, $this->output);
        }
        if (isset($config['report']['checkstyle'])) {
            $reportGenerators[] = CheckstyleReportGenerator::create($config, $this->output);
        }
        if (isset($config['report']['php'])) {
            $reportGenerators[] = PhpReportGenerator::create($config, $this->output);
        }

        return new ReportExporter($this->output, $reportGenerators);
    }
}
