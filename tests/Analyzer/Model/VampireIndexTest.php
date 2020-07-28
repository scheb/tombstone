<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class VampireIndexTest extends TestCase
{
    /**
     * @var VampireIndex
     */
    private $vampireIndex;

    /**
     * @var Vampire
     */
    private $expectedVampire1;

    /**
     * @var Vampire
     */
    private $expectedVampire2;

    private function createVampire(int $hash, string $invocationDate): Vampire
    {
        $vampire = $this->createMock(Vampire::class);
        $vampire
            ->expects($this->any())
            ->method('getHash')
            ->willReturn($hash);
        $vampire
            ->expects($this->any())
            ->method('getInvocationDate')
            ->willReturn($invocationDate);

        return $vampire;
    }

    protected function setUp(): void
    {
        $this->vampireIndex = new VampireIndex();

        $vampire1 = $this->createVampire(111, 'invalid');
        $vampire2 = $this->createVampire(111, '2020-01-01');
        $vampire3 = $this->createVampire(111, '2020-01-02');

        $vampire4 = $this->createVampire(222, '2020-01-02');
        $vampire5 = $this->createVampire(222, 'invalid');
        $vampire6 = $this->createVampire(222, '2020-01-01');

        $this->expectedVampire1 = $vampire3;
        $this->expectedVampire2 = $vampire4;

        $this->vampireIndex->addVampire($vampire1);
        $this->vampireIndex->addVampire($vampire2);
        $this->vampireIndex->addVampire($vampire3);
        $this->vampireIndex->addVampire($vampire4);
        $this->vampireIndex->addVampire($vampire5);
        $this->vampireIndex->addVampire($vampire6);
    }

    /**
     * @test
     */
    public function count_hasDuplicates_returnDeduplicatedNumberOfVampires(): void
    {
        $this->assertEquals(2, $this->vampireIndex->count());
    }

    /**
     * @test
     */
    public function getIterator_hasVampires_iterateDeduplicatedVampires(): void
    {
        $returnValue = iterator_to_array($this->vampireIndex);
        $this->assertCount(2, $returnValue);
        $this->assertContains($this->expectedVampire1, $returnValue, '', false, true);
        $this->assertContains($this->expectedVampire2, $returnValue, '', false, true);
    }
}
