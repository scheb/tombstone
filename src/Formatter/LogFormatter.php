<?php
namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Logging\LogFormat;
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
        return LogFormat::vampireToLog($vampire) . "\n";
    }
}
