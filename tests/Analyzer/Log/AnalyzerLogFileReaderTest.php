<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Log;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Cli\ConsoleOutputInterface;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogFileReader;
use Scheb\Tombstone\Analyzer\Log\AnalyzerLogProviderException;
use Scheb\Tombstone\Core\Format\AnalyzerLogFormatException;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerLogFileReaderTest extends TestCase
{
    /**
     * @var MockObject|RootPath
     */
    private $rootDir;

    /**
     * @var MockObject|ConsoleOutputInterface
     */
    private $output;

    /**
     * @var AnalyzerLogFileReader
     */
    private $logFileReader;

    protected function setUp(): void
    {
        $this->rootDir = $this->createMock(RootPath::class);
        $this->output = $this->createMock(ConsoleOutputInterface::class);
        $this->logFileReader = new AnalyzerLogFileReader($this->rootDir, $this->output);
    }

    /**
     * @test
     */
    public function readLogFile_fileNotReadable_throwAnalyzerLogProviderException(): void
    {
        $this->expectException(AnalyzerLogProviderException::class);
        $traversable = $this->logFileReader->readLogFile(__DIR__.'/fixtures/non_existent_file');
        iterator_to_array($traversable);
    }

    /**
     * @test
     */
    public function readLogFile_invalidLogData_outputErrorReturnValidVampires(): void
    {
        $this->output
            ->expects($this->never())
            ->method($this->anything());

        $traversable = $this->logFileReader->readLogFile(__DIR__.'/fixtures/allValid.tombstone');
        $items = iterator_to_array($traversable, false);

        $this->assertCount(2, $items);
        $this->assertContainsOnlyInstancesOf(Vampire::class, $items);
    }

    /**
     * @test
     */
    public function readLogFile_allValidData_returnVampires(): void
    {
        $this->output
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->matches('Ignoring invalid log data in "%s/malformedData.tombstone" on line 2'),
                $this->isInstanceOf(AnalyzerLogFormatException::class)
            );

        $traversable = $this->logFileReader->readLogFile(__DIR__.'/fixtures/malformedData.tombstone');
        $items = iterator_to_array($traversable, false);

        $this->assertCount(2, $items);
        $this->assertContainsOnlyInstancesOf(Vampire::class, $items);
    }
}
