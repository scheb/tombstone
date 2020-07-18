<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

use Scheb\Tombstone\Analyzer\Analyzer;
use Scheb\Tombstone\Analyzer\Config\ConfigurationLoader;
use Scheb\Tombstone\Analyzer\Config\YamlConfigProvider;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogDirectoryReader;
use Scheb\Tombstone\Analyzer\Log\LogReaderInterface;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Analyzer\Report\ConsoleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Report\PhpReportGenerator;
use Scheb\Tombstone\Analyzer\Report\ReportExporter;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorFactory;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorInterface;
use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Analyzer\VampireIndex;
use SebastianBergmann\FinderFacade\FinderFacade;
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
     * @var ConsoleOutput
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $output;

    protected function configure(): void
    {
        $this
            ->setName('tombstone')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Path to config file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = new ConsoleOutput($output);

        try {
            $this->doExecute();
        } catch (\Exception $e) {
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
        $configLoader = ConfigurationLoader::create();
        $config = $configLoader->loadConfiguration([new YamlConfigProvider($configFile)]);
        $rootDir = $config['rootDir'] ?? null;

        $tombstoneIndex = new TombstoneIndex($rootDir);
        $vampireIndex = new VampireIndex();
        $tombstoneExtractor = TombstoneExtractorFactory::create($config, $tombstoneIndex, $this->output);
        $logReaders = $this->createLogReaders($config);
        $analyzer = $this->createAnalyzer();

        $this->output->writeln('Scan source code ...');
        $files = $this->collectSourceFiles($config);
        $this->extractTombstones($files, $tombstoneExtractor);

        $this->output->writeln('Read logs ...');
        foreach ($logReaders as $logReader) {
            $logReader->collectVampires($vampireIndex);
        }

        $this->output->writeln('Analyze tombstones ...');
        $result = $analyzer->getResult($tombstoneIndex, $vampireIndex);

        $reportGenerators = [];
        if (isset($config['report']['console'])) {
            $reportGenerators[] = new ConsoleReportGenerator($this->output);
        }
        if (isset($config['report']['html'])) {
            $reportGenerators[] = new HtmlReportGenerator($config['report']['html'], $rootDir);
        }
        if (isset($config['report']['php'])) {
            $reportGenerators[] = new PhpReportGenerator($config['report']['php']);
        }

        $reportExporter = new ReportExporter($this->output, $reportGenerators);
        $reportExporter->generate($result);
    }

    private function createAnalyzer(): Analyzer
    {
        $matchingStrategies = [
            new MethodNameStrategy(),
            new PositionStrategy(),
        ];

        return new Analyzer($matchingStrategies);
    }

    /**
     * @return LogReaderInterface[]
     */
    private function createLogReaders(array $config): array
    {
        $logReaders = [];
        if (isset($config['logs']['directory'])) {
            $logReaders[] = AnalyzerLogDirectoryReader::create($config['logs']['directory'], $this->output);
        }
        if (isset($config['logs']['custom'])) {
            if (isset($config['logs']['custom']['file'])) {
                /** @psalm-suppress UnresolvableInclude */
                require_once $config['logs']['custom']['file'];
            }
            $reflectionClass = new \ReflectionClass($config['logs']['custom']['class']);
            if (!$reflectionClass->implementsInterface(LogReaderInterface::class)) {
                throw new \Exception(sprintf('Class %s must implement %s', $config['logs']['custom']['class'], LogReaderInterface::class));
            }

            /** @var LogReaderInterface $logReader */
            $logReader = $reflectionClass->newInstance();
            $logReaders[] = $logReader;
        }

        return $logReaders;
    }

    private function collectSourceFiles(array $config): array
    {
        $finder = new FinderFacade(
            $config['source']['directories'],
            $config['source']['excludes'],
            $config['source']['names'],
            $config['source']['notNames']
        );

        return $finder->findFiles();
    }

    private function extractTombstones(array $files, TombstoneExtractorInterface $tombstoneExtractor): void
    {
        $progress = $this->output->createProgressBar(\count($files));
        foreach ($files as $file) {
            $this->output->debug($file);
            $tombstoneExtractor->extractTombstones($file);
            $progress->advance();
        }
        $this->output->writeln();
    }
}
