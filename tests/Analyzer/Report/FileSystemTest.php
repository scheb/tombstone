<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Report\FileSystem;
use Scheb\Tombstone\Analyzer\Report\FileSystemException;
use Scheb\Tombstone\Tests\DirectoryHelper;
use Scheb\Tombstone\Tests\TestCase;

class FileSystemTest extends TestCase
{
    private const SOURCE_DIRECTORY = __DIR__.'/fixtures/source';
    private const TEST_DIRECTORY = __DIR__.'/fixtures/directory';

    protected function setUp(): void
    {
        $this->clean();
    }

    protected function tearDown(): void
    {
        $this->clean();
    }

    public function clean(): void
    {
        if (is_dir(self::TEST_DIRECTORY)) {
            DirectoryHelper::clearDirectory(self::TEST_DIRECTORY);
            rmdir(self::TEST_DIRECTORY);
        }
    }

    /**
     * @test
     */
    public function copyDirectoryFiles_cannotOpenSourceDirectory_throwFileSystemException(): void
    {
        $this->expectException(FileSystemException::class);
        FileSystem::copyDirectoryFiles(__DIR__.'/fixtures/invalidSource', self::TEST_DIRECTORY);
    }

    /**
     * @test
     */
    public function copyDirectoryFiles_targetIsWritable_copyAllFiles(): void
    {
        FileSystem::copyDirectoryFiles(self::SOURCE_DIRECTORY, self::TEST_DIRECTORY);

        $expectedFiles = DirectoryHelper::listDirectory(self::SOURCE_DIRECTORY);
        $createdFiles = DirectoryHelper::listDirectory(self::TEST_DIRECTORY);
        $this->assertEquals($expectedFiles, $createdFiles);
    }

    /**
     * @test
     */
    public function ensureDirectoryCreated_directoryAlreadyCreated_doNothing(): void
    {
        $this->assertDirectoryDoesNotExist(self::TEST_DIRECTORY);
        FileSystem::ensureDirectoryCreated(self::TEST_DIRECTORY);
        $this->assertDirectoryExists(self::TEST_DIRECTORY);
    }

    /**
     * @test
     */
    public function ensureDirectoryCreated_directoryNotCreated_createDirectory(): void
    {
        mkdir(self::TEST_DIRECTORY); // Make sure the directory already exists
        $this->assertDirectoryExists(self::TEST_DIRECTORY);
        FileSystem::ensureDirectoryCreated(self::TEST_DIRECTORY);
        $this->assertDirectoryExists(self::TEST_DIRECTORY);
    }

    /**
     * @test
     */
    public function ensureDirectoryCreated_cannotCreateDirectory_throwFileSystemException(): void
    {
        $this->expectException(FileSystemException::class);
        FileSystem::ensureDirectoryCreated(''); // Invalid directory path
    }

    /**
     * @test
     * @dataProvider getTestCasesForCreatePath
     */
    public function createPath_parentAndNameGiven_returnConcatenatedPath(string $parent, string $name, string $expectedResult): void
    {
        $this->assertEquals($expectedResult, FileSystem::createPath($parent, $name));
    }

    public function getTestCasesForCreatePath(): array
    {
        return [
            'root' => ['/root', 'name', DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'name'],
            'root with tailing slash' => ['/root/', 'name', DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'name'],
            'empty root' => ['', 'name', 'name'],
            'all empty' => ['', '', ''],
            'windows-style' => ['C:\\root', 'name', 'C:'.DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'name'],
            'windows-style with tailing slash' => ['C:\\root\\', 'name', 'C:'.DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'name'],
        ];
    }
}
