<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\AnalyzerDirectoryResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerDirectoryResultTest extends TestCase
{
    private function createDirectoryResult(int $deadCount, int $undeadCount, int $deletedCount): AnalyzerDirectoryResult
    {
        $result = $this->createMock(AnalyzerDirectoryResult::class);
        $result->expects($this->any())->method('getDeadCount')->willReturn($deadCount);
        $result->expects($this->any())->method('getUndeadCount')->willReturn($undeadCount);
        $result->expects($this->any())->method('getDeletedCount')->willReturn($deletedCount);

        return $result;
    }

    private function createFileResult(int $deadCount, int $undeadCount, int $deletedCount): AnalyzerFileResult
    {
        $result = $this->createMock(AnalyzerFileResult::class);
        $result->expects($this->any())->method('getDeadCount')->willReturn($deadCount);
        $result->expects($this->any())->method('getUndeadCount')->willReturn($undeadCount);
        $result->expects($this->any())->method('getDeletedCount')->willReturn($deletedCount);

        return $result;
    }

    private function createAnalyzerDirectoryResult(): AnalyzerDirectoryResult
    {
        $directoryResult1 = $this->createDirectoryResult(1, 1, 1);
        $directoryResult2 = $this->createDirectoryResult(0, 0, 1);

        $fileResult1 = $this->createFileResult(0, 2, 0);
        $fileResult2 = $this->createFileResult(3, 0, 0);

        return new AnalyzerDirectoryResult('dir', [$directoryResult1, $directoryResult2], [$fileResult1, $fileResult2]);
    }

    /**
     * @test
     */
    public function getDirectoryPath_directorySet_returnPath(): void
    {
        $result = new AnalyzerDirectoryResult('path/to/dir', [], []);
        $this->assertEquals('path/to/dir', $result->getDirectoryPath());
    }

    /**
     * @test
     */
    public function getDirectoryName_directorySet_returnLastSegment(): void
    {
        $result = new AnalyzerDirectoryResult('path/to/dir', [], []);
        $this->assertEquals('dir', $result->getDirectoryName());
    }

    /**
     * @test
     */
    public function getDirectoryName_rootDirectory_returnEmptyString(): void
    {
        $result = new AnalyzerDirectoryResult('', [], []);
        $this->assertEquals('', $result->getDirectoryName());
    }

    /**
     * @test
     */
    public function getDeadCount_directoryAndFileResultsGiven_returnSum(): void
    {
        $result = $this->createAnalyzerDirectoryResult();
        $this->assertEquals(4, $result->getDeadCount());
    }

    /**
     * @test
     */
    public function getUndeadCount_directoryAndFileResultsGiven_returnSum(): void
    {
        $result = $this->createAnalyzerDirectoryResult();
        $this->assertEquals(3, $result->getUndeadCount());
    }

    /**
     * @test
     */
    public function getDeletedCount_directoryAndFileResultsGiven_returnSum(): void
    {
        $result = $this->createAnalyzerDirectoryResult();
        $this->assertEquals(2, $result->getDeletedCount());
    }
}
