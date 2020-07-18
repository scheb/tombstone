<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Model\Vampire;

class AnalyzerLogFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return AnalyzerLogFormat::vampireToLog($vampire).PHP_EOL;
    }
}
