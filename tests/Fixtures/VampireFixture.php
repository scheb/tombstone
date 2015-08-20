<?php
namespace Scheb\Tombstone\Tests\Fixtures;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireFixture
{
    /**
     * @return Vampire
     */
    public static function getVampire()
    {
        $tombstone = new Tombstone('2014-01-01', 'author', 'label', 'file', 'line', 'method');
        return new Vampire('2015-01-01', 'invoker', $tombstone);
    }
}
