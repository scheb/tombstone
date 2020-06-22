<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Test;

use Scheb\Tombstone\Analyzer\FilePosition;

class FilePositionTest extends TestCase
{
    /**
     * @test
     */
    public function createPosition_fileAndLineGiven_returnString(): void
    {
        $returnValue = FilePosition::createPosition('file.php', 12);
        $this->assertEquals('file.php:12', $returnValue);
    }
}
