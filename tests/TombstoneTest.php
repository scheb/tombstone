<?php
namespace Scheb\Tombstone\Tests;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class TombstoneTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param string $date
     * @param string $author
     * @param string $label
     * @return Tombstone
     */
    private function createTombstone($date = '2015-08-19', $author = 'author', $label = 'label')
    {
        return new Tombstone($date, $author, $label, 'file', 'line', 'method');
    }

    /**
     * @test
     */
    public function getHash_valuesSet_returnCorrectHash()
    {
        $tombstone = $this->createTombstone();
        $hash = $tombstone->getHash();
        $this->assertEquals('ed8b6f2d651ec9bf688910c6bdd04d6a', $hash);
    }

    /**
     * @test
     */
    public function inscriptionEquals_sameValues_returnTrue()
    {
        $tombstone1 = $this->createTombstone();
        $tombstone2 = $this->createTombstone();
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider getTombstonesToCompare
     *
     * @param Tombstone $tombstone1
     * @param Tombstone $tombstone2
     */
    public function inscriptionEquals_differentInscription_returnFalse($tombstone1, $tombstone2)
    {
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertFalse($result);
    }

    /**
     * @return array
     */
    public function getTombstonesToCompare()
    {
        $reference = $this->createTombstone('2015-01-01', 'author', 'label');
        $tombstone1 = $this->createTombstone('2015-01-02', 'author', 'label');
        $tombstone2 = $this->createTombstone('2015-01-01', 'otherAuthor', 'label');
        $tombstone3 = $this->createTombstone('2015-01-01', 'author', 'otherLabel');

        return array(
            array($reference, $tombstone1),
            array($reference, $tombstone2),
            array($reference, $tombstone3),
        );
    }

    /**
     * @test
     */
    public function hasVampires_noVampiresSet_returnFalse()
    {
        $tombstone = $this->createTombstone();
        $this->assertFalse($tombstone->hasVampires());
    }

    /**
     * @test
     */
    public function hasVampires_vampireAdded_returnTrue()
    {
        $tombstone = $this->createTombstone();
        $tombstone->addVampire(new Vampire('2015-08-20', 'invoker', $tombstone));
        $this->assertTrue($tombstone->hasVampires());
    }
}
