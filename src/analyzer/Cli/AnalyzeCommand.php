<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Cli;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Scheb\Tombstone\Analyzer\Config\Configuration;
use Scheb\Tombstone\Analyzer\Config\ConfigurationLoader;
use Scheb\Tombstone\Analyzer\Config\YamlConfigProvider;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogDirectoryReader;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogFileReader;
use Scheb\Tombstone\Analyzer\Log\LogCollector;
use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;
use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Analyzer\Processing\Processor;
use Scheb\Tombstone\Analyzer\Processing\VampireMatcher;
use Scheb\Tombstone\Analyzer\Report\Checkstyle\CheckstyleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Console\ConsoleReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Html\HtmlReportGenerator;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\BreadCrumbRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DashboardRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryItemRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\DirectoryRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileSourceCodeRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\FileTombstoneListRenderer;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpFileFormatter;
use Scheb\Tombstone\Analyzer\Report\Html\Renderer\PhpSyntaxHighlighter;
use Scheb\Tombstone\Analyzer\Report\Php\PhpReportGenerator;
use Scheb\Tombstone\Analyzer\Report\ReportExporter;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractor;
use Scheb\Tombstone\Analyzer\Source\TombstoneExtractorInterface;
use Scheb\Tombstone\Analyzer\Source\TombstoneVisitor;
use Scheb\Tombstone\Core\Model\RootPath;
use SebastianBergmann\FinderFacade\FinderFacade;
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

        $sourceRootPath = new RootPath($config['source']['rootDirectory']);
        $tombstoneIndex = new TombstoneIndex();
        $vampireIndex = new VampireIndex();

        $tombstoneExtractor = $this->createTombstoneExtractor($tombstoneIndex);

        $logCollector = $this->createLogCollector($config, $sourceRootPath, $vampireIndex);
        $analyzer = $this->createAnalyzer();

        $this->output->writeln('Scan source code ...');
        $files = $this->collectSourceFiles($config);
        $this->extractTombstones($sourceRootPath, $files, $tombstoneExtractor);

        $this->output->writeln('Read logs ...');
        $logCollector->collectLogs();

        $this->output->writeln('Analyze tombstones ...');
        $result = $analyzer->process($tombstoneIndex, $vampireIndex);

        $reportGenerators = [];
        if (isset($config['report']['console'])) {
            $reportGenerators[] = new ConsoleReportGenerator($this->output);
        }
        if (isset($config['report']['html'])) {
            $breadCrumbRenderer = new BreadCrumbRenderer($sourceRootPath);
            $reportGenerators[] = new HtmlReportGenerator(
                $config['report']['html'],
                new DashboardRenderer(
                    $config['report']['html'],
                    $breadCrumbRenderer
                ),
                new DirectoryRenderer(
                    $config['report']['html'],
                    $breadCrumbRenderer,
                    new DirectoryItemRenderer()
                ),
                new FileRenderer(
                    $config['report']['html'],
                    $breadCrumbRenderer,
                    new FileTombstoneListRenderer(),
                    new FileSourceCodeRenderer(new PhpFileFormatter(new PhpSyntaxHighlighter()))
                )
            );
        }
        if (isset($config['report']['checkstyle'])) {
            $reportGenerators[] = new CheckstyleReportGenerator($config['report']['checkstyle']);
        }
        if (isset($config['report']['php'])) {
            $reportGenerators[] = new PhpReportGenerator($config['report']['php']);
        }

        $reportExporter = new ReportExporter($this->output, $reportGenerators);
        $reportExporter->generate($result);
    }

    public function createTombstoneExtractor(TombstoneIndex $tombstoneIndex): TombstoneExtractorInterface
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer());
        $traverser = new NodeTraverser();
        $extractor = new TombstoneExtractor($parser, $traverser, $tombstoneIndex);
        $traverser->addVisitor(new TombstoneVisitor($extractor));

        return $extractor;
    }

    private function createAnalyzer(): Processor
    {
        $matchingStrategies = [
            new MethodNameStrategy(),
            new PositionStrategy(),
        ];

        return new Processor(new VampireMatcher($matchingStrategies));
    }

    private function createLogCollector(array $config, RootPath $rootDir, VampireIndex $vampireIndex): LogCollector
    {
        $logReaders = [];
        if (isset($config['logs']['directory'])) {
            $logReaders[] = new AnalyzerLogDirectoryReader(
                new AnalyzerLogFileReader($rootDir, $this->output),
                $config['logs']['directory'],
                $this->output
            );
        }

        return new LogCollector($logReaders, $vampireIndex);
    }

    private function collectSourceFiles(array $config): array
    {
        $finder = new FinderFacade(
            [$config['source']['rootDirectory']],
            $config['source']['excludes'],
            $config['source']['names'],
            $config['source']['notNames']
        );

        return $finder->findFiles();
    }

    private function extractTombstones(RootPath $rootPath, array $files, TombstoneExtractorInterface $tombstoneExtractor): void
    {
        $progress = $this->output->createProgressBar(\count($files));
        foreach ($files as $file) {
            $this->output->debug($file);
            $tombstoneExtractor->extractTombstones($rootPath->createFilePath($file));
            $progress->advance();
        }
        $this->output->writeln();
    }
}
