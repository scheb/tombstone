<?php
namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\AnalyzerResult;

interface ReportGeneratorInterface
{
    /**
     * @param AnalyzerResult $result
     */
    public function generate(AnalyzerResult $result);
}
