<?php
namespace Tracing;

use Scheb\Tombstone\Tracing\PathNormalizer;

class PathNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function normalizeDirectorySeparator_unixPathGiven_returnSame()
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('/path/to/some/file.php');
        $this->assertEquals('/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     */
    public function normalizeDirectorySeparator_windowsPathGiven_changeDirectorySeparator()
    {
        $returnValue = PathNormalizer::normalizeDirectorySeparator('C:\\path\\to\\some\\file.php');
        $this->assertEquals('C:/path/to/some/file.php', $returnValue);
    }

    /**
     * @test
     * @dataProvider getTestCasesForRelativePath
     */
    public function makeRelativeTo_pathBeginsWithBase_returnRelativePath($path, $baseDir) {
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals('directory/file.php', $returnValue);
    }

    /**
     * @return array
     */
    public function getTestCasesForRelativePath()
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
     * @dataProvider getTestCasesForKeepingPath
     */
    public function makeRelativeTo_pathHasDifferentBase_returnSamePath($path, $baseDir) {
        $returnValue = PathNormalizer::makeRelativeTo($path, $baseDir);
        $this->assertEquals($path, $returnValue);
    }

    /**
     * @return array
     */
    public function getTestCasesForKeepingPath() {
        return array(
            array('/path/to/file.php', '/other/base'),
            array('/path/to/file.php', null),
            array('C:\\path\\to\\file.php', 'C:\\other\\path'),
            array('C:\\path\\to\\file.php', null),
        );
    }
}
