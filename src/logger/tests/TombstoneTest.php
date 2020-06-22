<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Test;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class TombstoneTest extends TestCase
{
    /**
     * @param string $file
     * @param string ...$arguments
     *
     * @return Tombstone
     */
    private function createTombstone(string $file, string ...$arguments): Tombstone
    {
        return new Tombstone($arguments, $file, 123, 'method', ['metaField' => 'metaValue']);
    }

    /**
     * @test
     */
    public function toString_windowsFilePath_normalizeFilePath(): void
    {
        $tombstone = $this->createTombstone('C:\\file', '2015-08-19', 'author');
        $this->assertEquals('C:/file', $tombstone->getFile());
    }

    /**
     * @test
     */
    public function toString_argumentsGiven_returnString(): void
    {
        $tombstone = $this->createTombstone('file', '2015-08-19', 'author');
        $this->assertEquals('tombstone("2015-08-19", "author")', (string) $tombstone);
    }

    /**
     * @test
     */
    public function getTombstoneDate_dateArgumentGiven_returnFirstDetectedTombstoneDate(): void
    {
        $tombstone = $this->createTombstone('file', 'label', '123', '2015-02-02', '2015-03-03');
        $this->assertEquals('2015-02-02', $tombstone->getTombstoneDate());
    }

    /**
     * @test
     */
    public function getTombstoneDate_noDateArgument_returnNull(): void
    {
        $tombstone = $this->createTombstone('file', 'label', '123');
        $this->assertNull($tombstone->getTombstoneDate());
    }

    /**
     * @test
     */
    public function getHash_valuesSet_returnCorrectHash(): void
    {
        $tombstone = $this->createTombstone('file');
        $hash = $tombstone->getHash();
        $this->assertEquals('f5825bfeac4236f671b94bab85752767', $hash);
    }

    /**
     * @test
     */
    public function inscriptionEquals_sameValues_returnTrue(): void
    {
        $tombstone1 = $this->createTombstone('file');
        $tombstone2 = $this->createTombstone('file');
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
        $reference = $this->createTombstone('file', '2015-01-01', 'author', 'label');
        $tombstone1 = $this->createTombstone('file', '2015-01-02', 'author', 'label');
        $tombstone2 = $this->createTombstone('file', '2015-01-01', 'otherAuthor', 'label');
        $tombstone3 = $this->createTombstone('file', '2015-01-01', 'author', 'otherLabel');

        return [
            [$reference, $tombstone1],
            [$reference, $tombstone2],
            [$reference, $tombstone3],
        ];
    }

    /**
     * @test
     */
    public function hasVampires_noVampiresSet_returnFalse(): void
    {
        $tombstone = $this->createTombstone('file');
        $this->assertFalse($tombstone->hasVampires());
    }

    /**
     * @test
     */
    public function hasVampires_vampireAdded_returnTrue(): void
    {
        $tombstone = $this->createTombstone('file');
        $tombstone->addVampire(new Vampire('2015-08-20', 'invoker', [], $tombstone));
        $this->assertTrue($tombstone->hasVampires());
    }
}
