<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\Matching\PositionStrategy;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

class PositionStrategyTest extends AbstractMatchingStrategyTest
{
    private function stubTombstoneIndexReturnsTombstone(?Tombstone $tombstone): void
    {
        $this->tombstoneIndex
            ->expects($this->any())
            ->method('getInFileAndLine')
            ->with($this->isInstanceOf(FilePathInterface::class), self::LINE)
            ->willReturn($tombstone);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_tombstoneMatches_returnTombstone(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubTombstoneIndexReturnsTombstone($matchedTombstone);
        $this->stubVampireInscriptionEquals($matchedTombstone, true);

        $strategy = new PositionStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertSame($matchedTombstone, $returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_noTombstoneAtPosition_returnNull(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubTombstoneIndexReturnsTombstone(null);
        $this->stubVampireInscriptionEquals($matchedTombstone, true);

        $strategy = new PositionStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_tombstoneInscriptionDifferent_returnNull(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubTombstoneIndexReturnsTombstone($matchedTombstone);
        $this->stubVampireInscriptionEquals($matchedTombstone, false);

        $strategy = new PositionStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertNull($returnValue);
    }
}
