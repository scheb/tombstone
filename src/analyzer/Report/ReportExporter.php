<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;

class ReportExporter
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * @var ReportGeneratorInterface[]
     */
    private $reportGenerators;

    public function __construct(ConsoleOutput $output, array $reportGenerators)
    {
        $this->output = $output;
        $this->reportGenerators = $reportGenerators;
    }

    public function generate(AnalyzerResult $result): void
    {
        $i = 0;
        foreach ($this->reportGenerators as $reportGenerator) {
            if ($i > 0) {
                $this->output->writeln();
            }
            $generatorName = $reportGenerator->getName();
            ++$i;
            $this->output->write('Generate '.$generatorName.' report... ');
            try {
                $reportGenerator->generate($result);
                $this->output->writeln('Done');
            } catch (\Throwable $e) {
                $this->output->writeln('Could not generate '.$generatorName.' report: '.$e->getMessage());
                $this->output->debug($e->getTraceAsString());
            }
        }
    }
}
