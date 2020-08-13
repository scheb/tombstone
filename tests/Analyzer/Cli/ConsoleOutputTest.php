<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Cli;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutput;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Tests\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputTest extends TestCase
{
    /**
     * @var MockObject|OutputInterface
     */
    private $outputInterface;

    /**
     * @var ConsoleOutputInterface
     */
    private $consoleOutput;

    protected function setUp(): void
    {
        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter
            ->expects($this->any())
            ->method('isDecorated')
            ->willReturn(false);

        $this->outputInterface = $this->createMock(OutputInterface::class);
        $this->outputInterface
            ->expects($this->any())
            ->method('getFormatter')
            ->willReturn($formatter);

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

        $this->consoleOutput->createProgressBar(20);
    }

    /**
     * @test
     */
    public function error_debugEnabledNoException_writeOnlyErrorMessage(): void
    {
        $this->outputInterface
            ->expects($this->any())
            ->method('isDebug')
            ->willReturn(true);

        $this->outputInterface
            ->expects($this->once())
            ->method('writeln')
            ->with('<error>error message</error>');

        $this->consoleOutput->error('error message');
    }

    /**
     * @test
     */
    public function error_debugDisabledWithException_writeOnlyErrorMessage(): void
    {
        $this->outputInterface
            ->expects($this->any())
            ->method('isDebug')
            ->willReturn(false);

        $this->outputInterface
            ->expects($this->once())
            ->method('writeln')
            ->with('<error>error message</error>');

        $this->consoleOutput->error('error message', new \Exception('exception message'));
    }

    /**
     * @test
     */
    public function error_debugEnabledWithException_writeWithExceptionDetails(): void
    {
        $this->outputInterface
            ->expects($this->any())
            ->method('isDebug')
            ->willReturn(true);

        $this->outputInterface
            ->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                ['<error>error message</error>'],
                [$this->matches('Exception: exception message at %s'.DIRECTORY_SEPARATOR.'ConsoleOutputTest.php line %i')]
            );

        $this->consoleOutput->error('error message', new \Exception('exception message'));
    }
}
