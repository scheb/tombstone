<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\AnalyzerFileResult;
use Scheb\Tombstone\Analyzer\Model\AnalyzerResult;
use Scheb\Tombstone\Core\Model\AbsoluteFilePath;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\Fixture;
use Scheb\Tombstone\Tests\TestCase;

class AnalyzerResultTest extends TestCase
{
    private function createTombstone(string $referencePath): Tombstone
    {
        return $this->createTombstoneWithPath($this->createFile($referencePath));
    }

    private function createTombstoneWithPath(FilePathInterface $path): Tombstone
    {
        $tombstone = $this->createMock(Tombstone::class);
        $tombstone
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($path);

        return $tombstone;
    }

    private function createVampire(string $referencePath): Vampire
    {
        return $this->createVampireWithPath($this->createFile($referencePath));
    }

    private function createVampireWithPath(FilePathInterface $path): Vampire
    {
        $tombstone = $this->createMock(Vampire::class);
        $tombstone
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($path);

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

    private function createRelativeFilePath(string $relativePath): RelativeFilePath
    {
        $file = $this->createMock(RelativeFilePath::class);
        $file
            ->expects($this->any())
            ->method('getReferencePath')
            ->willReturn($relativePath);
        $file
            ->expects($this->any())
            ->method('getRelativePath')
            ->willReturn($relativePath);

        return $file;
    }

    private function createAbsoluteFilePath(string $relativePath): AbsoluteFilePath
    {
        $file = $this->createMock(AbsoluteFilePath::class);
        $file
            ->expects($this->any())
            ->method('getReferencePath')
            ->willReturn($relativePath);
        $file
            ->expects($this->any())
            ->method('getAbsolutePath')
            ->willReturn($relativePath);

        return $file;
    }

    private function assertFileResult(string $filePath, array $dead, array $undead, array $deleted, AnalyzerFileResult $result): void
    {
        $this->assertEquals($filePath, $result->getFile()->getReferencePath());
        $this->assertSame($dead, $result->getDead());
        $this->assertSame($undead, $result->getUndead());
        $this->assertSame($deleted, $result->getDeleted());
    }

    /**
     * @test
     */
    public function getFileResults_multipleFilesWithDifferentResults_returnAnalyzerFileResults(): void
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

        $perFileResult = $result->getFileResults();

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

    /**
     * @test
     */
    public function getRootDirectoryResult_filesWithAbsolutePathGiven_ignoreTheseResults(): void
    {
        $deadTombstone = $this->createTombstoneWithPath($this->createAbsoluteFilePath('/absolute/path1'));
        $undeadTombstone = $this->createTombstoneWithPath($this->createAbsoluteFilePath('/absolute/path2'));
        $deletedVampire = $this->createVampireWithPath($this->createAbsoluteFilePath('/absolute/path3'));

        $result = new AnalyzerResult(
            [$deadTombstone],
            [$undeadTombstone],
            [$deletedVampire]
        );

        $rootDirectoryResult = $result->getRootDirectoryResult();

        $this->assertEquals(0, $rootDirectoryResult->getDeadCount());
        $this->assertEquals(0, $rootDirectoryResult->getUndeadCount());
        $this->assertEquals(0, $rootDirectoryResult->getDeletedCount());

        $this->assertCount(0, $rootDirectoryResult->getSubDirectoryResults());
        $this->assertCount(0, $rootDirectoryResult->getFileResults());
    }

    /**
     * @test
     */
    public function getRootDirectoryResult_filesWithRelativePathGiven_returnResultTree(): void
    {
        $deadTombstone1 = $this->createTombstoneWithPath($this->createRelativeFilePath('dir1/file1'));
        $deadTombstone2 = $this->createTombstoneWithPath($this->createRelativeFilePath('dir1/file1'));
        $deadTombstone3 = $this->createTombstoneWithPath($this->createRelativeFilePath('file1'));
        $deadTombstone4 = $this->createTombstoneWithPath($this->createRelativeFilePath('dir1/dir1.1/file1'));

        $undeadTombstone1 = $this->createTombstoneWithPath($this->createRelativeFilePath('file1'));
        $undeadTombstone2 = $this->createTombstoneWithPath($this->createRelativeFilePath('file2'));
        $undeadTombstone3 = $this->createTombstoneWithPath($this->createRelativeFilePath('dir2/dir2.1/file1'));

        $deletedVampire1 = $this->createVampireWithPath($this->createRelativeFilePath('dir1/dir1.2/file1'));

        $result = new AnalyzerResult(
            [$deadTombstone1, $deadTombstone2, $deadTombstone3, $deadTombstone4],
            [$undeadTombstone1, $undeadTombstone2, $undeadTombstone3],
            [$deletedVampire1]
        );

        $rootResult = $result->getRootDirectoryResult();

        // Root
        $this->assertEquals(4, $rootResult->getDeadCount());
        $this->assertEquals(3, $rootResult->getUndeadCount());
        $this->assertEquals(1, $rootResult->getDeletedCount());
        $this->assertCount(2, $rootResult->getSubDirectoryResults());
        $this->assertCount(2, $rootResult->getFileResults());
        $this->assertFileResult('file1', [$deadTombstone3], [$undeadTombstone1], [], $rootResult->getFileResults()[0]);
        $this->assertFileResult('file2', [], [$undeadTombstone2], [], $rootResult->getFileResults()[1]);

        // dir1
        $dir1Result = $rootResult->getSubDirectoryResults()[0];
        $this->assertEquals(3, $dir1Result->getDeadCount());
        $this->assertEquals(0, $dir1Result->getUndeadCount());
        $this->assertEquals(1, $dir1Result->getDeletedCount());
        $this->assertCount(2, $dir1Result->getSubDirectoryResults());
        $this->assertCount(1, $dir1Result->getFileResults());
        $this->assertFileResult('dir1/file1', [$deadTombstone1, $deadTombstone2], [], [], $dir1Result->getFileResults()[0]);

        // dir1.1
        $dir11Result = $dir1Result->getSubDirectoryResults()[0];
        $this->assertEquals(1, $dir11Result->getDeadCount());
        $this->assertEquals(0, $dir11Result->getUndeadCount());
        $this->assertEquals(0, $dir11Result->getDeletedCount());
        $this->assertCount(0, $dir11Result->getSubDirectoryResults());
        $this->assertCount(1, $dir11Result->getFileResults());
        $this->assertFileResult('dir1/dir1.1/file1', [$deadTombstone4], [], [], $dir11Result->getFileResults()[0]);

        // dir 1.2
        $dir12Result = $dir1Result->getSubDirectoryResults()[1];
        $this->assertEquals(0, $dir12Result->getDeadCount());
        $this->assertEquals(0, $dir12Result->getUndeadCount());
        $this->assertEquals(1, $dir12Result->getDeletedCount());
        $this->assertCount(0, $dir12Result->getSubDirectoryResults());
        $this->assertCount(1, $dir12Result->getFileResults());
        $this->assertFileResult('dir1/dir1.2/file1', [], [], [$deletedVampire1], $dir12Result->getFileResults()[0]);

        // dir2
        $dir2Result = $rootResult->getSubDirectoryResults()[1];
        $this->assertEquals(0, $dir2Result->getDeadCount());
        $this->assertEquals(1, $dir2Result->getUndeadCount());
        $this->assertEquals(0, $dir2Result->getDeletedCount());
        $this->assertCount(1, $dir2Result->getSubDirectoryResults());
        $this->assertCount(0, $dir2Result->getFileResults());

        // dir 2.1
        $dir21Result = $dir2Result->getSubDirectoryResults()[0];
        $this->assertEquals(0, $dir21Result->getDeadCount());
        $this->assertEquals(1, $dir21Result->getUndeadCount());
        $this->assertEquals(0, $dir21Result->getDeletedCount());
        $this->assertCount(0, $dir21Result->getSubDirectoryResults());
        $this->assertCount(1, $dir21Result->getFileResults());
        $this->assertFileResult('dir2/dir2.1/file1', [], [$undeadTombstone3], [], $dir21Result->getFileResults()[0]);
    }

    /**
     * @test
     */
    public function void(): void
    {
        $deadTombstone = Fixture::getTombstone('arg1');
        $undeadTombstone = Fixture::getTombstone('arg2');
        $deletedVampire = Fixture::getVampire('arg3');

        $result = new AnalyzerResult(
            [$deadTombstone],
            [$undeadTombstone],
            [$deletedVampire]
        );

        $unserializedResult = unserialize(serialize($result));
        $this->assertEquals($result, $unserializedResult);
    }
}
