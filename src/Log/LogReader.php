<?php

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireIndex;
use Scheb\Tombstone\Logging\AnalyzerLogFormat;

class LogReader
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
        while (!feof($handle)) {
            $line = fgets($handle);
            $vampire = AnalyzerLogFormat::logToVampire($line);
            if ($vampire) {
                $this->vampires->addVampire($vampire);
            }
        }
        fclose($handle);
    }

    public function getVampires(): VampireIndex
    {
        return $this->vampires;
    }
}
