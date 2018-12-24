<?php

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\AnalyzerResult;

interface ReportGeneratorInterface
{
    public function generate(AnalyzerResult $result): void;
}
