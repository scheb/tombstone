<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report\Console;

use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Report\Console\ConsoleReportGenerator;
use Scheb\Tombstone\Tests\Analyzer\Report\fixtures\AnalyzerResultFixture;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleReportGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function generate_resultGiven_generateConsoleOutput(): void
    {
        $result = AnalyzerResultFixture::getAnalyzerResult();

        $outputBuffer = new BufferedOutput();
        $generator = new ConsoleReportGenerator(new ConsoleOutput($outputBuffer));
        $generator->generate($result);

        $consoleOutput = $outputBuffer->fetch();
        file_put_contents(__DIR__.'/consoleOutput.actual.log', $consoleOutput);
        $this->assertStringMatchesFormatFile(__DIR__.'/consoleOutput.log', $consoleOutput);
    }
}
