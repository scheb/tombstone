<?php

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return AnalyzerLogFormat::vampireToLog($vampire)."\n";
    }
}
