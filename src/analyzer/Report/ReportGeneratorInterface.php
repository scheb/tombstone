<?php

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\AnalyzerResult;

interface ReportGeneratorInterface
{
    public function getName(): string;

    public function generate(AnalyzerResult $result): void;
}
