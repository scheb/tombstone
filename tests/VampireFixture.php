<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTraceFrame;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class VampireFixture
{
    public const ROOT_DIR = __DIR__;

    public static function getVampire(string ...$arguments): Vampire
    {
        $rootPath = new RootPath(self::ROOT_DIR);
        $tombstone = new Tombstone($arguments, $rootPath->createFilePath('file'), 123, 'method', ['metaField' => 'metaValue']);
        $stackTrace = [new StackTraceFrame($rootPath->createFilePath('/path/to/file1.php'), 11, 'ClassName->method')];

        return new Vampire('2015-01-01', 'invoker', $stackTrace, $tombstone);
    }
}
