<?php
namespace Scheb\Tombstone\Analyzer\Tests;

use Scheb\Tombstone\Analyzer\FilePosition;

class FilePositionTest extends TestCase
{
    /**
     * @test
     */
    public function createPosition_fileAndLineGiven_returnString()
    {
        $returnValue = FilePosition::createPosition('file.php', 12);
        $this->assertEquals('file.php:12', $returnValue);
    }
}
