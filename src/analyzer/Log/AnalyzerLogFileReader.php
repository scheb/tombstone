<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Core\Format\AnalyzerLogFormat;
use Scheb\Tombstone\Core\Format\AnalyzerLogFormatException;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Vampire;

class AnalyzerLogFileReader
{
    /**
     * @var RootPath
     */
    private $rootDir;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    public function __construct(RootPath $rootDir, ConsoleOutputInterface $output)
    {
        $this->rootDir = $rootDir;
        $this->output = $output;
    }

    /**
     * @return \Traversable<int, Vampire>
     */
    public function readLogFile(string $file): \Traversable
    {
        $handle = @fopen($file, 'r');
        if (false === $handle) {
            throw new AnalyzerLogProviderException(sprintf('Could not read log file %s', $file));
        }

        $lineNumber = 0;
        while (false !== ($line = fgets($handle))) {
            ++$lineNumber;
            try {
                yield AnalyzerLogFormat::logToVampire($line, $this->rootDir);
            } catch (AnalyzerLogFormatException $e) {
                $this->output->error(sprintf('Ignoring invalid log data in "%s" on line %s', $file, $lineNumber), $e);
            }
        }
        fclose($handle);
    }
}
