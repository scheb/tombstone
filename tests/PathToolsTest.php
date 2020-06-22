<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test;

use Scheb\Tombstone\Analyzer\PathTools;

class PathToolsTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideAbsolutePaths
     */
    public function makePathAbsolute_absolutePathGiven_returnSamePath(string $path): void
    {
        $returnValue = PathTools::makePathAbsolute($path, '/root/path');
        $this->assertEquals($path, $returnValue);
    }

    public function provideAbsolutePaths(): array
    {
        return [
            // Unix
            ['/absolute/path'],

            // Windows
            ['\\\\NetworkComputer\\Path'],
            ['\\\\.\\D:'],
            ['\\\\.\\c:'],
            ['C:\\Windows'],
            ['c:\\windows'],
            ['C:/Windows'],
            ['c:/windows'],
        ];
    }

    /**
     * @test
     * @dataProvider provideRelativePaths
     */
    public function makePathAbsolute_provideRelativePaths_returnPathWithinRoot(string $relativePath, string $expectedAbsolutePath): void
    {
        $returnValue = PathTools::makePathAbsolute($relativePath, '/root/path');
        $this->assertEquals($expectedAbsolutePath, $returnValue);
    }

    public function provideRelativePaths(): array
    {
        $normalizedRootPath = DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'path';
        $expectedAbsolutePath = $normalizedRootPath.DIRECTORY_SEPARATOR.'relative'.DIRECTORY_SEPARATOR.'path';

        return [
            ['', $normalizedRootPath],
            ['.', $normalizedRootPath],
            ['..', $normalizedRootPath.DIRECTORY_SEPARATOR.'..'],

            // Unix
            ['relative/path', $expectedAbsolutePath],
            ['./relative/path', $expectedAbsolutePath],
            ['././relative/path', $expectedAbsolutePath],

            // Windows
            ['relative\\path', $expectedAbsolutePath],
            ['.\\relative\\path', $expectedAbsolutePath],
            ['.\\.\\relative\\path', $expectedAbsolutePath],
        ];
    }
}
