<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test\Cli;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Test\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputTest extends TestCase
{
    /**
     * @var MockObject|OutputInterface
     */
    private $outputInterface;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    protected function setUp()
    {
        $this->outputInterface = $this->createMock(OutputInterface::class);
        $this->outputInterface
            ->expects($this->any())
            ->method('getFormatter')
            ->willReturn($this->createMock(OutputFormatterInterface::class));

        $this->consoleOutput = new ConsoleOutput($this->outputInterface);
    }

    /**
     * @test
     */
    public function write_stringGiven_writeToOutput(): void
    {
        $this->outputInterface
            ->expects($this->once())
            ->method('write')
            ->with('message');

        $this->consoleOutput->write('message');
    }

    /**
     * @test
     */
    public function writeln_stringGiven_writeToOutput(): void
    {
        $this->outputInterface
            ->expects($this->once())
            ->method('writeln')
            ->with('message');

        $this->consoleOutput->writeln('message');
    }

    /**
     * @test
     */
    public function writeln_nothingGiven_writeEmptyLine(): void
    {
        $this->outputInterface
            ->expects($this->once())
            ->method('writeln')
            ->with('');

        $this->consoleOutput->writeln();
    }

    /**
     * @test
     */
    public function debug_debugEnabled_writeMessage(): void
    {
        $this->outputInterface
            ->expects($this->any())
            ->method('isDebug')
            ->willReturn(true);

        $this->outputInterface
            ->expects($this->once())
            ->method('writeln')
            ->with('message');

        $this->consoleOutput->debug('message');
    }

    /**
     * @test
     */
    public function debug_debugDisabled_writeNothing(): void
    {
        $this->outputInterface
            ->expects($this->any())
            ->method('isDebug')
            ->willReturn(false);

        $this->outputInterface
            ->expects($this->never())
            ->method('writeln');

        $this->consoleOutput->debug('message');
    }

    /**
     * @test
     */
    public function createProgressBar_withWidth_returnConfiguredProgressBar(): void
    {
        $this->outputInterface
            ->expects($this->atLeastOnce())
            ->method('write');

        $progressBar = $this->consoleOutput->createProgressBar(20);

        $this->assertEquals(50, $progressBar->getBarWidth());
        $this->assertEquals(20, $progressBar->getMaxSteps());
    }
}
