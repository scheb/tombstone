<?php

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Exception\LogReaderException;
use Scheb\Tombstone\Analyzer\VampireIndex;
use Scheb\Tombstone\Logging\AnalyzerLogFormat;

class AnalyzerLogReader
{
    /**
     * @var VampireIndex
     */
    private $vampires;

    /**
     * @param $vampires
     */
    public function __construct(VampireIndex $vampires)
    {
        $this->vampires = $vampires;
    }

    public function aggregateLog(string $file): void
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new LogReaderException('Could not read log file '.$file);
        }

        while (!feof($handle)) {
            $line = fgets($handle);
            $vampire = AnalyzerLogFormat::logToVampire($line);
            if ($vampire) {
                $this->vampires->addVampire($vampire);
            }
        }
        fclose($handle);
    }
}
