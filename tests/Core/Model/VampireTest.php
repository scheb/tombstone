<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Core\Model;

use Scheb\Tombstone\Core\Model\StackTrace;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;
use Scheb\Tombstone\Tests\VampireFixture;

class VampireTest extends TestCase
{
    /**
     * @test
     * @dataProvider getResultsForInscriptionEquals
     */
    public function inscriptionEquals_vampireWithTombstoneGiven_getResultFromTombstone(bool $result): void
    {
        $otherTombstone = $this->createMock(Tombstone::class);
        $tombstone = $this->createMock(Tombstone::class);
        $vampire = new Vampire('2015-01-01', 'invoker', new StackTrace(), $tombstone, []);

        $tombstone
            ->expects($this->any())
            ->method('inscriptionEquals')
            ->with($this->identicalTo($otherTombstone))
            ->willReturn($result);

        $returnValue = $vampire->inscriptionEquals($otherTombstone);
        $this->assertEquals($result, $returnValue);
    }

    public function getResultsForInscriptionEquals(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test
     */
    public function withTombstone_differentTombstoneObjectGiven_returnDuplicateWithThatTombstone()
    {
        $tombstone = $this->createMock(Tombstone::class);
        $vampire = VampireFixture::getVampire();
        $newVampire = $vampire->withTombstone($tombstone);

        $this->assertSame($tombstone, $newVampire->getTombstone());
        $this->assertEquals($vampire->getInvocationDate(), $newVampire->getInvocationDate());
        $this->assertEquals($vampire->getInvoker(), $newVampire->getInvoker());
        $this->assertSame($vampire->getStackTrace(), $newVampire->getStackTrace());
        $this->assertEquals($vampire->getMetadata(), $newVampire->getMetadata());
    }

    /**
     * @test
     */
    public function getHash_valuesSet_returnCorrectHash(): void
    {
        $vampire = VampireFixture::getVampire();
        $hash = $vampire->getHash();
        $this->assertEquals(1397077150, $hash);
    }
}
