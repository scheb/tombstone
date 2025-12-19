<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\Tombstone\Core\Model\RootPath;
use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneTest extends TestCase
{
    private const ROOT_DIR = '/path/to';

    private static function createTombstone(string $file, string ...$arguments): Tombstone
    {
        $rootPath = new RootPath(self::ROOT_DIR);

        return new Tombstone('tombstone', $arguments, $rootPath->createFilePath($file), 123, 'method');
    }

    /**
     * @test
     */
    #[Test]
    public function toString_argumentsGiven_returnString(): void
    {
        $tombstone = $this->createTombstone('file', '2015-08-19', 'author');
        $this->assertEquals('tombstone("2015-08-19", "author")', (string) $tombstone);
    }

    /**
     * @test
     */
    #[Test]
    public function getTombstoneDate_dateArgumentGiven_returnFirstDetectedTombstoneDate(): void
    {
        $tombstone = $this->createTombstone('file', 'label', '123', '2015-02-02', '2015-03-03');
        $this->assertEquals('2015-02-02', $tombstone->getTombstoneDate());
    }

    /**
     * @test
     */
    #[Test]
    public function getTombstoneDate_noDateArgument_returnNull(): void
    {
        $tombstone = $this->createTombstone('file', 'label', '123');
        $this->assertNull($tombstone->getTombstoneDate());
    }

    /**
     * @test
     */
    #[Test]
    public function getHash_valuesSet_returnCorrectHash(): void
    {
        $tombstone = $this->createTombstone('file');
        $hash = $tombstone->getHash();
        $this->assertEquals(1385567777, $hash);
    }

    /**
     * @test
     */
    #[Test]
    public function inscriptionEquals_sameValues_returnTrue(): void
    {
        $tombstone1 = $this->createTombstone('file');
        $tombstone2 = $this->createTombstone('file');
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    #[Test]
    public function inscriptionEquals_absolutePathVsRelativePath_returnTrue(): void
    {
        $tombstone1 = $this->createTombstone('file');
        $tombstone2 = $this->createTombstone('/path/to/file');
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @dataProvider provideTombstonesToCompare
     */
    #[Test]
    #[DataProvider('provideTombstonesToCompare')]
    public function inscriptionEquals_differentInscription_returnFalse(Tombstone $tombstone1, Tombstone $tombstone2): void
    {
        $result = $tombstone1->inscriptionEquals($tombstone2);
        $this->assertFalse($result);
    }

    public static function provideTombstonesToCompare(): array
    {
        $reference = self::createTombstone('file', '2015-01-01', 'author', 'label');
        $tombstone1 = self::createTombstone('file', '2015-01-02', 'author', 'label');
        $tombstone2 = self::createTombstone('file', '2015-01-01', 'otherAuthor', 'label');
        $tombstone3 = self::createTombstone('file', '2015-01-01', 'author', 'otherLabel');

        return [
            [$reference, $tombstone1],
            [$reference, $tombstone2],
            [$reference, $tombstone3],
        ];
    }

    /**
     * @test
     */
    #[Test]
    public function hasVampires_noVampiresSet_returnFalse(): void
    {
        $tombstone = $this->createTombstone('file');
        $this->assertFalse($tombstone->hasVampires());
    }

    /**
     * @test
     */
    #[Test]
    public function hasVampires_vampireAdded_returnTrue(): void
    {
        $tombstone = $this->createTombstone('file');
        $tombstone->addVampire(new Vampire('2015-08-20', 'invoker', new StackTrace(), $tombstone));
        $this->assertTrue($tombstone->hasVampires());
    }

    /**
     * @test
     */
    #[Test]
    public function getVampires_noVampiresSet_returnEmptyArray(): void
    {
        $tombstone = $this->createTombstone('file');
        $this->assertCount(0, $tombstone->getVampires());
    }

    /**
     * @test
     */
    #[Test]
    public function getVampires_vampireAdded_returnVampires(): void
    {
        $tombstone = $this->createTombstone('file');
        $vampire = new Vampire('2015-08-20', 'invoker', new StackTrace(), $tombstone);
        $tombstone->addVampire($vampire);

        $returnValue = $tombstone->getVampires();
        $this->assertCount(1, $returnValue);
        $this->assertContains($vampire, $returnValue);
    }
}
