<?php

namespace Scheb\Tombstone\Test\Fixtures;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireFixture
{
    public static function getVampire(string ...$arguments): Vampire
    {
        $tombstone = new Tombstone($arguments, 'file', 123, 'method');

        return new Vampire('2015-01-01', 'invoker', $tombstone);
    }
}
