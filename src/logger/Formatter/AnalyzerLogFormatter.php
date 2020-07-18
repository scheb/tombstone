<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Formatter;

use Scheb\Tombstone\Core\Format\AnalyzerLogFormat;
use Scheb\Tombstone\Core\Model\Vampire;

class AnalyzerLogFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return AnalyzerLogFormat::vampireToLog($vampire).PHP_EOL;
    }
}
