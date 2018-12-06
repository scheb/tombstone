<?php

namespace Scheb\Tombstone\Test;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class TombstoneTest extends TestCase
{
    /**
     * @param string $date
     * @param string $author
     * @param string $label
     *
     * @return Tombstone
     */
    private function createTombstone(string $date = '2015-08-19', ?string $author = 'author', ?string $label = 'label'): Tombstone
    {
        return new Tombstone($date, $author, $label, 'file', 123, 'method');
    }

    /**
     * @test
     */
    public function toString_withLabel_returnString(): void
    {
        $tombstone = $this->createTombstone();
        $this->assertEquals('tombstone("2015-08-19", "author", "label")', (string) $tombstone);
    }

    /**
     * @test
     */
    public function toString_withoutLabel_returnString(): void
    {
        $tombstone = $this->createTombstone('2015-08-19', 'author', null);
        $this->assertEquals('tombstone("2015-08-19", "author")', (string) $tombstone);
    }

    /**
     * @test
     */
    public function getHash_valuesSet_returnCorrectHash(): void
    {
        $tombstone = $this->createTombstone();
        $hash = $tombstone->getHash();
        $this->assertEquals('25538d2a7fcf16500d7e4feb075ff8bb', $hash);
    }

    /**
     * @test
     */
    public function inscriptionEquals_sameValues_returnTrue(): void
    {
        $tombstone1 = $this->createTombstone();
        $tombstone2 = $this->createTombstone();
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider provideTombstonesToCompare
     */
    public function inscriptionEquals_differentInscription_returnFalse(Tombstone $tombstone1, Tombstone $tombstone2): void
    {
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertFalse($result);
    }

    public function provideTombstonesToCompare(): array
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
    public function hasVampires_noVampiresSet_returnFalse(): void
    {
        $tombstone = $this->createTombstone();
        $this->assertFalse($tombstone->hasVampires());
    }

    /**
     * @test
     */
    public function hasVampires_vampireAdded_returnTrue(): void
    {
        $tombstone = $this->createTombstone();
        $tombstone->addVampire(new Vampire('2015-08-20', 'invoker', $tombstone));
        $this->assertTrue($tombstone->hasVampires());
    }
}
