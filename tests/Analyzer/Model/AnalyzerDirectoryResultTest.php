<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\AnalyzerDirectoryResult;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerDirectoryResultTest extends TestCase
{
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
}
