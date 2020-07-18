<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\Exception\LogReaderException;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Formatter\AnalyzerLogFormat;

class AnalyzerLogFileReader
{
    public function aggregateLog(string $file, VampireIndex $vampireIndex): void
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new LogReaderException(sprintf('Could not read log file %s', $file));
        }

        while (false !== ($line = fgets($handle))) {
            $vampire = AnalyzerLogFormat::logToVampire($line);
            if ($vampire) {
                $vampireIndex->addVampire($vampire);
            }
        }
        fclose($handle);
    }
}
