<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Model\RootPath;
use SebastianBergmann\FinderFacade\FinderFacade;

class AnalyzerLogProvider implements LogProviderInterface
{
    /**
     * @var AnalyzerLogFileReader
     */
    private $logFileReader;

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    public function __construct(AnalyzerLogFileReader $logFileReader, string $logDir, ConsoleOutputInterface $output)
    {
        $this->logFileReader = $logFileReader;
        $this->logDir = $logDir;
        $this->output = $output;
    }

    public static function create(array $config, ConsoleOutputInterface $consoleOutput): LogProviderInterface
    {
        $rootDir = new RootPath($config['source_code']['root_directory']);

        return new self(
            new AnalyzerLogFileReader($rootDir, $consoleOutput),
            $config['logs']['directory'],
            $consoleOutput
        );
    }

    public function getVampires(): iterable
    {
        $finder = new FinderFacade([$this->logDir], [], ['*.tombstone']);
        $files = $finder->findFiles();

        $this->output->writeln('Read analyzer log data ...');
        $progress = $this->output->createProgressBar(\count($files));

        foreach ($files as $file) {
            // This is done to reset keys
            foreach ($this->logFileReader->readLogFile($file) as $vampire) {
                yield $vampire;
            }
            $progress->advance();
        }
        $this->output->writeln();
    }
}
