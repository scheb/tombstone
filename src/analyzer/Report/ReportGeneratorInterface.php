<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;

interface ReportGeneratorInterface
{
    public static function create(array $config, ConsoleOutputInterface $consoleOutput): self;

    public function getName(): string;

    public function generate(AnalyzerResult $result): void;
}
