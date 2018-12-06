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
        return array(
            array('/path/to/directory/file.php', '/path/to'),
            array('/path/to/directory/file.php', '/path/to/'),
            array('C:\\path\\to\\directory\\file.php', 'C:\\path\\to'),
            array('C:\\path\\to\\directory\\file.php', 'C:\\path\\to\\'),
        );
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
        return array(
            array('/path/to/file.php', '/other/base'),
            array('/path/to/file.php', null),
            array('C:\\path\\to\\file.php', 'C:\\other\\path'),
            array('C:\\path\\to\\file.php', null),
        );
    }
}
