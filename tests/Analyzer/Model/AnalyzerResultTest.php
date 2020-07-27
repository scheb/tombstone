<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerResultTest extends TestCase
{
    private function createTombstone(string $referencePath): Tombstone
    {
        $tombstone = $this->createMock(Tombstone::class);
        $tombstone
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($this->createFile($referencePath));

        return $tombstone;
    }

    private function createVampire(string $referencePath): Vampire
    {
        $tombstone = $this->createMock(Vampire::class);
        $tombstone
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($this->createFile($referencePath));

        return $tombstone;
    }

    private function createFile(string $referencePath): FilePathInterface
    {
        $file = $this->createMock(FilePathInterface::class);
        $file
            ->expects($this->any())
            ->method('getReferencePath')
            ->willReturn($referencePath);

        return $file;
    }

    /**
     * @test
     */
    public function getPerFile_multipleFilesWithDifferentResults_returnAnalyzerFileResults(): void
    {
        $deadTombstone1 = $this->createTombstone('file3');
        $deadTombstone2 = $this->createTombstone('file1');
        $deadTombstone3 = $this->createTombstone('file1');
        $deadTombstone4 = $this->createTombstone('file2');

        $undeadTombstone1 = $this->createTombstone('file2');
        $undeadTombstone2 = $this->createTombstone('file2');
        $undeadTombstone3 = $this->createTombstone('file1');

        $deletedVampire1 = $this->createVampire('file3');
        $deletedVampire2 = $this->createVampire('file1');
        $deletedVampire3 = $this->createVampire('file2');

        $result = new AnalyzerResult(
            [$deadTombstone1, $deadTombstone2, $deadTombstone3, $deadTombstone4],
            [$undeadTombstone1, $undeadTombstone2, $undeadTombstone3],
            [$deletedVampire1, $deletedVampire2, $deletedVampire3]
        );

        $perFileResult = $result->getPerFile();

        $file1Result = $perFileResult[0];
        $this->assertEquals('file1', $file1Result->getFile()->getReferencePath());
        $this->assertEquals(2, $file1Result->getDeadCount());
        $this->assertEquals(1, $file1Result->getUndeadCount());
        $this->assertEquals(1, $file1Result->getDeletedCount());
        $this->assertSame([$deadTombstone2, $deadTombstone3], $file1Result->getDead());
        $this->assertSame([$undeadTombstone3], $file1Result->getUndead());
        $this->assertSame([$deletedVampire2], $file1Result->getDeleted());

        $file2Result = $perFileResult[1];
        $this->assertEquals('file2', $file2Result->getFile()->getReferencePath());
        $this->assertEquals(1, $file2Result->getDeadCount());
        $this->assertEquals(2, $file2Result->getUndeadCount());
        $this->assertEquals(1, $file2Result->getDeletedCount());
        $this->assertSame([$deadTombstone4], $file2Result->getDead());
        $this->assertSame([$undeadTombstone1, $undeadTombstone2], $file2Result->getUndead());
        $this->assertSame([$deletedVampire3], $file2Result->getDeleted());

        $file3Result = $perFileResult[2];
        $this->assertEquals('file3', $file3Result->getFile()->getReferencePath());
        $this->assertEquals(1, $file3Result->getDeadCount());
        $this->assertEquals(0, $file3Result->getUndeadCount());
        $this->assertEquals(1, $file3Result->getDeletedCount());
        $this->assertSame([$deadTombstone1], $file3Result->getDead());
        $this->assertSame([], $file3Result->getUndead());
        $this->assertSame([$deletedVampire1], $file3Result->getDeleted());
    }
}
