<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use SebastianBergmann\FinderFacade\FinderFacade;

class AnalyzerLogDirectoryReader implements LogReaderInterface
{
    /**
     * @var AnalyzerLogFileReader
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

    public function __construct(AnalyzerLogFileReader $logReader, string $logDir, ConsoleOutput $output)
    {
        $this->logReader = $logReader;
        $this->logDir = $logDir;
        $this->output = $output;
    }

    public static function create(string $logDir, ConsoleOutput $output): self
    {
        return new AnalyzerLogDirectoryReader(new AnalyzerLogFileReader(), $logDir, $output);
    }

    public function collectVampires(VampireIndex $vampireIndex): void
    {
        $finder = new FinderFacade([$this->logDir], [], ['*.tombstone']);
        $files = $finder->findFiles();

        $progress = $this->output->createProgressBar(\count($files));
        foreach ($files as $file) {
            $this->logReader->aggregateLog($file, $vampireIndex);
            $progress->advance();
        }
        $this->output->writeln();
    }
}
