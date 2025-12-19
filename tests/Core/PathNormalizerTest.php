<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\Tombstone\Core\PathNormalizer;
use Scheb\Tombstone\Tests\TestCase;

class PathNormalizerTest extends TestCase
{
    /**
     * @test
     */
    #[Test]
    public function normalizeDirectorySeparator_unixPathGiven_returnSame(): void
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('/path/to/some/file.php');
        $this->assertEquals('/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     */
    #[Test]
    public function normalizeDirectorySeparator_windowsPathGiven_changeDirectorySeparator(): void
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('C:\\path\\to\\some\\file.php');
        $this->assertEquals('C:/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     * @dataProvider provideDifferentPlatformPaths
     */
    #[Test]
    #[DataProvider('provideDifferentPlatformPaths')]
    public function normalizeDirectorySeparatorForEnvironment_unixPathGiven_returnSame(string $path, string $expectedPath): void
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparatorForEnvironment($path);
        $this->assertEquals($expectedPath, $returnValue);
    }

    public static function provideDifferentPlatformPaths(): array
    {
        return [
            ['/dir/file.php', DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'file.php'],
            ['C:\\dir\\file.php', 'C:'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'file.php'],
        ];
    }
}
