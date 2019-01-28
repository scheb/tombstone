<?php

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use SebastianBergmann\FinderFacade\FinderFacade;

class AnalyzerLogDirectoryScanner implements LogReaderInterface
{
    /**
     * @var AnalyzerLogReader
     */
    private $logReader;

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * @param AnalyzerLogReader $logReader
     * @param string            $logDir
     */
    public function __construct(AnalyzerLogReader $logReader, string $logDir, ConsoleOutput $output)
    {
        $this->logReader = $logReader;
        $this->logDir = $logDir;
        $this->output = $output;
    }

    public function collectVampires(): void
    {
        $finder = new FinderFacade([$this->logDir], [], ['*.tombstone']);
        $files = $finder->findFiles();

        $progress = $this->output->createProgressBar(count($files));
        foreach ($files as $file) {
            $this->logReader->aggregateLog($file);
            $progress->advance();
        }
        $this->output->writeln();
    }
}
