<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Analyzer\Report\ReportExporter;
use Scheb\Tombstone\Analyzer\Report\ReportGeneratorInterface;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ReportExporterTest extends TestCase
{
    /**
     * @var BufferedOutput
     */
    private $outputBuffer;

    /**
     * @var MockObject|ReportGeneratorInterface
     */
    private $generator1;

    /**
     * @var MockObject|ReportGeneratorInterface
     */
    private $generator2;

    /**
     * @var ReportExporter
     */
    private $exporter;

    protected function setUp(): void
    {
        $this->outputBuffer = new BufferedOutput();
        $output = new ConsoleOutput($this->outputBuffer);
        $this->generator1 = $this->createGenerator('Generator1');
        $this->generator2 = $this->createGenerator('Generator2');
        $this->exporter = new ReportExporter($output, [$this->generator1, $this->generator2]);
    }

    /**
     * @return MockObject|ReportGeneratorInterface
     */
    private function createGenerator(string $name): ReportGeneratorInterface
    {
        $generator = $this->createMock(ReportGeneratorInterface::class);
        $generator
            ->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $generator;
    }

    /**
     * @test
     */
    public function generate_multipleGenerators_executeEachGenerator(): void
    {
        $result = $this->createMock(AnalyzerResult::class);

        $this->generator1
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($result));

        $this->generator2
            ->expects($this->once())
            ->method('generate')
            ->with($this->identicalTo($result));

        $this->exporter->generate($result);
    }

    /**
     * @test
     */
    public function generate_multipleGenerators_printProgressOnConsole(): void
    {
        $this->exporter->generate($this->createMock(AnalyzerResult::class));

        $consoleOutput = $this->outputBuffer->fetch();
        $this->assertEquals("Generate Generator1 report... Done\n\nGenerate Generator2 report... Done\n", $consoleOutput);
    }

    /**
     * @test
     */
    public function generate_exceptionThrown_continueGenerating(): void
    {
        $this->generator1
            ->expects($this->once())
            ->method('generate')
            ->willThrowException(new \Exception('error'));

        $this->generator2
            ->expects($this->once())
            ->method('generate');

        $this->exporter->generate($this->createMock(AnalyzerResult::class));
    }

    /**
     * @test
     */
    public function generate_exceptionThrown_printError(): void
    {
        $this->generator1
            ->expects($this->any())
            ->method('generate')
            ->willThrowException(new \Exception('error'));

        $this->exporter->generate($this->createMock(AnalyzerResult::class));

        $consoleOutput = $this->outputBuffer->fetch();
        $this->assertEquals("Generate Generator1 report... Could not generate Generator1 report\n\nGenerate Generator2 report... Done\n", $consoleOutput);
    }
}
