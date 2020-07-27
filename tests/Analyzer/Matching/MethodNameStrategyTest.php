<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Matching;

use Scheb\Tombstone\Analyzer\Matching\MethodNameStrategy;

class MethodNameStrategyTest extends AbstractMatchingStrategyTest
{
    private const METHOD = 'method';

    private function stubVampireHasMethod(): void
    {
        $this->vampire
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn(self::METHOD);
    }

    private function stubTombstoneIndexReturnsTombstones(array $tombstones): void
    {
        $this->tombstoneIndex
            ->expects($this->any())
            ->method('getInMethod')
            ->with(self::METHOD)
            ->willReturn($tombstones);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_singleTombstoneMatches_returnTombstone(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubVampireHasMethod();
        $this->stubTombstoneIndexReturnsTombstones([$matchedTombstone]);
        $this->stubVampireInscriptionEquals($matchedTombstone, true);

        $strategy = new MethodNameStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertSame($matchedTombstone, $returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_multipleTombstonesInMethod_returnFirstMatchingInscription(): void
    {
        $matchedTombstone1 = $this->createTombstone();
        $matchedTombstone2 = $this->createTombstone();
        $matchedTombstone3 = $this->createTombstone();
        $this->stubVampireHasMethod();
        $this->stubTombstoneIndexReturnsTombstones([$matchedTombstone1, $matchedTombstone2, $matchedTombstone3]);
        $this->stubMultipleVampireInscriptionEquals([
            [$matchedTombstone1, false],
            [$matchedTombstone2, true],
            [$matchedTombstone3, true],
        ]);

        $strategy = new MethodNameStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertSame($matchedTombstone2, $returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_vampireHasNoMethod_returnNull(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubTombstoneIndexReturnsTombstones([$matchedTombstone]);
        $this->stubVampireInscriptionEquals($matchedTombstone, true);

        $strategy = new MethodNameStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_noTombstoneInMethod_returnNull(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubVampireHasMethod();
        $this->stubTombstoneIndexReturnsTombstones([]);
        $this->stubVampireInscriptionEquals($matchedTombstone, true);

        $strategy = new MethodNameStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertNull($returnValue);
    }

    /**
     * @test
     */
    public function matchVampireToTombstone_tombstoneInscriptionDifferent_returnNull(): void
    {
        $matchedTombstone = $this->createTombstone();
        $this->stubVampireHasMethod();
        $this->stubTombstoneIndexReturnsTombstones([$matchedTombstone]);
        $this->stubVampireInscriptionEquals($matchedTombstone, false);

        $strategy = new MethodNameStrategy();
        $returnValue = $strategy->matchVampireToTombstone($this->vampire, $this->tombstoneIndex);

        $this->assertNull($returnValue);
    }
}
