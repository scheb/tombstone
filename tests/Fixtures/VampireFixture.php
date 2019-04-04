<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test\Fixtures;

use Scheb\Tombstone\StackTraceFrame;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireFixture
{
    public static function getVampire(string ...$arguments): Vampire
    {
        $tombstone = new Tombstone($arguments, 'file', 123, 'method', ['metaField' => 'metaValue']);
        $stackTrace = [new StackTraceFrame('/path/to/file1.php', 11, 'ClassName->method')];

        return new Vampire('2015-01-01', 'invoker', $stackTrace, $tombstone);
    }
}
