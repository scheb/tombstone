<?php

namespace Scheb\Tombstone\Test\Fixtures;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireFixture
{
    public static function getVampire(string $date = '2014-01-01', ?string $author = 'author', ?string $label = 'label'): Vampire
    {
        $tombstone = new Tombstone($date, $author, $label, 'file', 123, 'method');

        return new Vampire('2015-01-01', 'invoker', $tombstone);
    }
}
