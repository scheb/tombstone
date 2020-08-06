<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use PHPUnit\Framework\TestCase;
use Scheb\Tombstone\Core\Model\AbsoluteFilePath;
use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\RootPath;

class RootPathTest extends TestCase
{
    /**
     * @test
     */
    public function construct_relativePath_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be absolute');

        new RootPath('relative/path');
    }

    /**
     * @test
     * @dataProvider provideDenormalizedPaths
     */
    public function getAbsolutePath_denormalizedPathGiven_returnNormalizedPath(string $path, string $normalizedPath): void
    {
        $rootPath = new RootPath($path);
        $this->assertEquals($normalizedPath, $rootPath->getAbsolutePath());
    }

    public function provideDenormalizedPaths(): array
    {
        return [
            ['/path/to/test', '/path/to/test/'],
            ['C:\\Some\\Path', 'C:/Some/Path/'],
            ['\\mixed/separator\\paths/', '/mixed/separator/paths/'],
        ];
    }

    /**
     * @test
     */
    public function getAbsolutePath_withoutTailingBackslashGiven_returnWithBackslash(): void
    {
        $rootPath = new RootPath('/path/missing/slash');
        $this->assertEquals('/path/missing/slash/', $rootPath->getAbsolutePath());
    }

    /**
     * @test
     * @dataProvider provideRelativePathTestCases
     */
    public function createFilePath_pathWithinRoot_returnRelativeFilePath(string $rootPath, string $path, string $expectedAbsolutePath): void
    {
        $expectedRelativePath = 'directory/file.php';
        $rootPath = new RootPath($rootPath);

        /** @var RelativeFilePath $returnValue */
        $returnValue = $rootPath->createFilePath($path);

        $this->assertInstanceOf(RelativeFilePath::class, $returnValue);
        $this->assertEquals($expectedRelativePath, $returnValue->getRelativePath());
        $this->assertEquals($expectedAbsolutePath, $returnValue->getAbsolutePath());
        $this->assertEquals($expectedRelativePath, $returnValue->getReferencePath());
    }

    /**
     * @test
     */
    public function getReferencePath_rootPathGiven_returnAbsolutePath(): void
    {
        $rootPath = new RootPath('/path/missing/slash');
        $this->assertEquals('/path/missing/slash/', $rootPath->getReferencePath());
    }

    public function provideRelativePathTestCases(): array
    {
        return [
            ['/path/to', '/path/to/directory/file.php', '/path/to/directory/file.php'],
            ['/path/to/', '/path/to/directory/file.php', '/path/to/directory/file.php'],
            ['C:\\path\\to', 'C:\\path\\to\\directory\\file.php', 'C:/path/to/directory/file.php'],
            ['C:\\path\\to\\', 'C:\\path\\to\\directory\\file.php', 'C:/path/to/directory/file.php'],
            ['C:\\path/to', 'C:\\path\\to\\directory\\file.php', 'C:/path/to/directory/file.php'],
            ['C:\\path/to\\', 'C:\\path\\to\\directory\\file.php', 'C:/path/to/directory/file.php'],
            ['C:\\path/to/', 'C:\\path\\to\\directory\\file.php', 'C:/path/to/directory/file.php'],
            ['C:/path/to', 'C:\\path\\to\\directory/file.php', 'C:/path/to/directory/file.php'],
        ];
    }

    /**
     * @test
     * @dataProvider provideRelativePathNotPossible
     */
    public function createFilePath_pathOutsideRoot_returnAbsoluteFilePath(string $rootPath, string $path, string $expectedAbsolutePath)
    {
        $rootPath = new RootPath($rootPath);

        /** @var AbsoluteFilePath $returnValue */
        $returnValue = $rootPath->createFilePath($path);

        $this->assertInstanceOf(AbsoluteFilePath::class, $returnValue);
        $this->assertEquals($expectedAbsolutePath, $returnValue->getAbsolutePath());
        $this->assertEquals($expectedAbsolutePath, $returnValue->getReferencePath());
    }

    public function provideRelativePathNotPossible(): array
    {
        return [
            ['/other/base', '/path/to/file.php', '/path/to/file.php'],
            ['C:\\other\\path', 'C:\\path\\to\\file.php', 'C:/path/to/file.php'],
        ];
    }

    /**
     * @test
     * @dataProvider provideRelativePaths
     */
    public function createFilePath_relativePathGiven_generateAbsolutePath(string $rootPath, string $path, string $expectedAbsolutePath)
    {
        $rootPath = new RootPath($rootPath);

        /** @var RelativeFilePath $returnValue */
        $returnValue = $rootPath->createFilePath($path);

        $this->assertInstanceOf(RelativeFilePath::class, $returnValue);
        $this->assertEquals($expectedAbsolutePath, $returnValue->getAbsolutePath());
    }

    public function provideRelativePaths(): array
    {
        return [
            ['/root/path', '', '/root/path/'],
            ['/root/path', '.', '/root/path/'],
            ['/root/path', '..', '/root/path/..'],

            // Unix
            ['/root/path', 'relative/path', '/root/path/relative/path'],
            ['/root/path', './relative/path', '/root/path/relative/path'],
            ['/root/path', './relative/path', '/root/path/relative/path'],
            ['/root/path', './../relative/path', '/root/path/../relative/path'],

            // Windows
            ['C:\\Root', 'relative\\path', 'C:/Root/relative/path'],
            ['C:\\Root', '.\\relative\\path', 'C:/Root/relative/path'],
            ['C:\\Root', '.\\.\\relative\\path', 'C:/Root/relative/path'],
            ['C:\\Root', '.\\..\\relative\\path', 'C:/Root/../relative/path'],
        ];
    }
}
