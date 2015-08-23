<?php
namespace Tracing;

use Scheb\Tombstone\Tracing\PathNormalizer;

class PathNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getBaseDirsToTest
     */
    public function makeRelativeTo_pathBeginsWithBase_returnRelativePath($baseDir) {
        $path = '/path/to/file.php';
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals('file.php', $returnValue);
    }

    public function getBaseDirsToTest()
    {
        return array(
            array('/path/to'),
            array('/path/to/'),
        );
    }

    /**
     * @test
     */
    public function makeRelativeTo_pathHasDifferentBase_returnSamePath() {
        $path = '/path/to/file.php';
        $baseDir = '/other/base';
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals('/path/to/file.php', $returnValue);
    }

    /**
     * @test
     */
    public function makeRelativeTo_noBaseDirGiven_returnSamePath() {
        $path = '/path/to/file.php';
        $returnValue = PathNormalizer::makeRelativeTo($path, null);
        $this->assertEquals('/path/to/file.php', $returnValue);
    }
}
