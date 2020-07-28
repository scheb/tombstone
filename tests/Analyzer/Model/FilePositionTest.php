<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\FilePosition;
use Scheb\Tombstone\Tests\TestCase;

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
