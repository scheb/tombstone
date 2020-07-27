<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Tests\TestCase;

class StackTraceFrameTest extends TestCase
{
    /**
     * @test
     */
    public function getHash_valuesSet_returnCorrectHash(): void
    {
        $rootPath = new RootPath('/root');
        $frame = new StackTraceFrame($rootPath->createFilePath('file1'), 123, 'method');

        $this->assertEquals(2913941853, $frame->getHash());
    }
}
