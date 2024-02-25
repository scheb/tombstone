<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Matching;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Matching\MatchingStrategyInterface;
use Scheb\Tombstone\Analyzer\Matching\VampireMatcher;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

class VampireMatcherTest extends TestCase
{
    /**
     * @var MockObject|MatchingStrategyInterface
     */
    private $matchingStrategy1;

    /**
     * @var MockObject|MatchingStrategyInterface
     */
    private $matchingStrategy2;

    /**
     * @var MockObject|MatchingStrategyInterface
     */
    private $matchingStrategy3;

    /**
     * @var VampireMatcher
     */
    private $matcher;

    /**
     * @var MockObject|Vampire
     */
    private $vampire;

    /**
     * @var MockObject|TombstoneIndex
     */
    private $tombstoneIndex;

    /**
     * @var MockObject|Tombstone
     */
    private $matchingTombstone;

    protected function setUp(): void
    {
        $this->matchingStrategy1 = $this->createMock(MatchingStrategyInterface::class);
        $this->matchingStrategy2 = $this->createMock(MatchingStrategyInterface::class);
        $this->matchingStrategy3 = $this->createMock(MatchingStrategyInterface::class);
        $this->vampire = $this->createMock(Vampire::class);
        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);
        $this->matchingTombstone = $this->createMock(Tombstone::class);

        $this->matcher = new VampireMatcher([
            $this->matchingStrategy1,
            $this->matchingStrategy2,
        ]);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_matches_returnFirstMatchingTombstone(): void
    {
        $this->matchingStrategy1
            ->expects($this->once())
            ->method('matchVampireToTombstone')
            ->with($this->vampire, $this->tombstoneIndex)
            ->willReturn(null);

        $this->matchingStrategy2
            ->expects($this->once())
            ->method('matchVampireToTombstone')
            ->with($this->vampire, $this->tombstoneIndex)
            ->willReturn($this->matchingTombstone);

        $this->matchingStrategy3
            ->expects($this->never())
            ->method('matchVampireToTombstone');

        $returnValue = $this->matcher->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);
        $this->assertSame($this->matchingTombstone, $returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_noMatch_returnNull(): void
    {
        $this->matchingStrategy1
            ->expects($this->any())
            ->method('matchVampireToTombstone')
            ->willReturn(null);

        $this->matchingStrategy2
            ->expects($this->any())
            ->method('matchVampireToTombstone')
            ->willReturn(null);

        $this->matchingStrategy3
            ->expects($this->any())
            ->method('matchVampireToTombstone')
            ->willReturn(null);

        $returnValue = $this->matcher->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);
        $this->assertNull($returnValue);
    }
}
