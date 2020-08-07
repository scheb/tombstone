<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Processing;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Analyzer\Model\VampireIndex;
use Scheb\Tombstone\Analyzer\Processing\Processor;
use Scheb\Tombstone\Analyzer\Processing\VampireMatcher;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class ProcessorTest extends TestCase
{
    /**
     * @var MockObject|VampireMatcher
     */
    private $matcher;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var Vampire[]
     */
    private $vampires;

    /**
     * @var MockObject|VampireIndex
     */
    private $vampireIndex;

    /**
     * @var Tombstone[]
     */
    private $tombstones;

    /**
     * @var MockObject|TombstoneIndex
     */
    private $tombstoneIndex;

    protected function setUp(): void
    {
        $this->vampires = [];
        $this->vampireIndex = $this->createMock(VampireIndex::class);
        $this->vampireIndex
            ->expects($this->any())
            ->method('getIterator')
            ->willReturnCallback(function () {
                return new \ArrayIterator($this->vampires);
            });

        $this->tombstones = [];
        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);
        $this->tombstoneIndex
            ->expects($this->any())
            ->method('getIterator')
            ->willReturnCallback(function () {
                return new \ArrayIterator($this->tombstones);
            });

        $this->matcher = $this->createMock(VampireMatcher::class);
        $this->processor = new Processor($this->matcher);
    }

    private function stubIndexHasVampire(Vampire $vampire): void
    {
        $this->vampires[] = $vampire;
    }

    private function stubIndexHasTombstone(Tombstone $tombstone): void
    {
        $this->tombstones[] = $tombstone;
    }

    private function stubMatchVampireToTombstone(Vampire $vampire, ?Tombstone $tombstone): void
    {
        $this->matcher
            ->expects($this->any())
            ->method('matchVampireToTombstone')
            ->with($vampire, $this->tombstoneIndex)
            ->willReturn($tombstone);
    }

    /**
     * @test
     */
    public function process_tombstoneHasVampire_setVampireAndResultsAsUndead(): void
    {
        $tombstone = $this->createMock(Tombstone::class);
        $vampire = $this->createMock(Vampire::class);
        $vampireWithMatchingTombstone = $this->createMock(Vampire::class);

        $vampire
            ->expects($this->any())
            ->method('withTombstone')
            ->with($tombstone)
            ->willReturn($vampireWithMatchingTombstone);

        $tombstone
            ->expects($this->once())
            ->method('addVampire')
            ->with($this->identicalTo($vampireWithMatchingTombstone));
        $tombstone
            ->expects($this->any())
            ->method('hasVampires')
            ->willReturn(true);

        $this->stubIndexHasTombstone($tombstone);
        $this->stubIndexHasVampire($vampire);

        $this->stubMatchVampireToTombstone($vampire, $tombstone);

        $result = $this->processor->process($this->tombstoneIndex, $this->vampireIndex);

        $this->assertEquals(1, $result->getUndeadCount());
        $this->assertSame([$tombstone], $result->getUndead());

        $this->assertEquals(0, $result->getDeadCount());
        $this->assertEquals([], $result->getDead());
        $this->assertEquals(0, $result->getDeletedCount());
        $this->assertEquals([], $result->getDeleted());
    }

    /**
     * @test
     */
    public function process_tombstoneHasNoVampire_resultsAsDead(): void
    {
        $tombstone = $this->createMock(Tombstone::class);
        $tombstone
            ->expects($this->never())
            ->method('addVampire');
        $tombstone
            ->expects($this->any())
            ->method('hasVampires')
            ->willReturn(false);

        $this->stubIndexHasTombstone($tombstone);
        $result = $this->processor->process($this->tombstoneIndex, $this->vampireIndex);

        $this->assertEquals(1, $result->getDeadCount());
        $this->assertSame([$tombstone], $result->getDead());

        $this->assertEquals(0, $result->getDeletedCount());
        $this->assertEquals([], $result->getDeleted());
        $this->assertEquals(0, $result->getUndeadCount());
        $this->assertEquals([], $result->getUndead());
    }

    /**
     * @test
     */
    public function process_vampireWithoutTombstone_resultsAsDeleted(): void
    {
        $vampire = $this->createMock(Vampire::class);
        $this->stubIndexHasVampire($vampire);

        $this->stubMatchVampireToTombstone($vampire, null);

        $result = $this->processor->process($this->tombstoneIndex, $this->vampireIndex);

        $this->assertEquals(1, $result->getDeletedCount());
        $this->assertSame([$vampire], $result->getDeleted());

        $this->assertEquals(0, $result->getDeadCount());
        $this->assertEquals([], $result->getDead());
        $this->assertEquals(0, $result->getUndeadCount());
        $this->assertEquals([], $result->getUndead());
    }
}
