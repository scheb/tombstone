<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use SebastianBergmann\FinderFacade\FinderFacade;

class AnalyzerLogDirectoryReader implements LogReaderInterface
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
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(AnalyzerLogFileReader $logFileReader, string $logDir, ConsoleOutput $output)
    {
        $this->logFileReader = $logFileReader;
        $this->logDir = $logDir;
        $this->output = $output;
    }

    public function iterateVampires(): \Traversable
    {
        $finder = new FinderFacade([$this->logDir], [], ['*.tombstone']);
        $files = $finder->findFiles();

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
