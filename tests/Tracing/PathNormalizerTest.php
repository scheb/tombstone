<?php

namespace Tracing;

use Scheb\Tombstone\Test\TestCase;
use Scheb\Tombstone\Tracing\PathNormalizer;

class PathNormalizerTest extends TestCase
{
    /**
     * @test
     */
    public function normalizeDirectorySeparator_unixPathGiven_returnSame(): void
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('/path/to/some/file.php');
        $this->assertEquals('/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     */
    public function normalizeDirectorySeparator_windowsPathGiven_changeDirectorySeparator(): void
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('C:\\path\\to\\some\\file.php');
        $this->assertEquals('C:/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     * @dataProvider provideTestCasesForRelativePath
     */
    public function makeRelativeTo_pathBeginsWithBase_returnRelativePath(string $path, string $baseDir): void
    {
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals('directory/file.php', $returnValue);
    }

    public function provideTestCasesForRelativePath(): array
    {
        return [
            ['/path/to/directory/file.php', '/path/to'],
            ['/path/to/directory/file.php', '/path/to/'],
            ['C:\\path\\to\\directory\\file.php', 'C:\\path\\to'],
            ['C:\\path\\to\\directory\\file.php', 'C:\\path\\to\\'],
            ['C:\\path\\to\\directory\\file.php', 'C:\\path/to'],
            ['C:\\path\\to\\directory\\file.php', 'C:\\path/to\\'],
            ['C:\\path\\to\\directory\\file.php', 'C:\\path/to/'],
            ['C:\\path\\to\\directory/file.php', 'C:/path/to'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTestCasesForKeepingPath
     */
    public function makeRelativeTo_pathHasDifferentBase_returnSamePath(string $path, ?string $baseDir): void
    {
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals($path, $returnValue);
    }

    public function provideTestCasesForKeepingPath(): array
    {
        return [
            ['/path/to/file.php', '/other/base'],
            ['/path/to/file.php', null],
            ['C:\\path\\to\\file.php', 'C:\\other\\path'],
            ['C:\\path\\to\\file.php', null],
        ];
    }
}
