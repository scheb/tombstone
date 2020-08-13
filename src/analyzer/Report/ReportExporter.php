<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;

class ReportExporter
{
    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var ReportGeneratorInterface[]
     */
    private $reportGenerators;

    public function __construct(ConsoleOutputInterface $output, array $reportGenerators)
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
                $this->output->error('Could not generate '.$generatorName.' report', $e);
                $this->output->debug($e->getTraceAsString());
            }
        }
    }
}
