<?php
namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Vampire;

class LogFormatter implements FormatterInterface
{

    /**
     * Formats a Vampire for the log
     *
     * @param Vampire $vampire
     *
     * @return string
     */
    public function format(Vampire $vampire)
    {
        return AnalyzerLogFormat::vampireToLog($vampire) . "\n";
    }
}
