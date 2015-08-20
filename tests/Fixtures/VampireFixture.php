<?php
namespace Scheb\Tombstone\Tests\Fixtures;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class VampireFixture
{
    /**
     * @param string $date
     * @param string $author
     * @param string $label
     *
     * @return Vampire
     */
    public static function getVampire($date = '2014-01-01', $author = 'author', $label = 'label')
    {
        $tombstone = new Tombstone($date, $author, $label, 'file', 'line', 'method');
        return new Vampire('2015-01-01', 'invoker', $tombstone);
    }
}
