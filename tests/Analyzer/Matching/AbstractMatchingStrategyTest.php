<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Matching;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Tests\TestCase;

abstract class AbstractMatchingStrategyTest extends TestCase
{
    protected const REFERENCE_PATH = 'referenceFilePath';
    protected const LINE = 123;

    /**
     * @var MockObject|Vampire
     */
    protected $vampire;

    /**
     * @var MockObject|TombstoneIndex
     */
    protected $tombstoneIndex;

    protected function setUp(): void
    {
        $file = $this->createMock(FilePathInterface::class);
        $file
            ->expects($this->any())
            ->method('getReferencePath')
            ->willReturn(self::REFERENCE_PATH);

        $this->vampire = $this->createMock(Vampire::class);
        $this->vampire
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($file);
        $this->vampire
            ->expects($this->any())
            ->method('getLine')
            ->willReturn(self::LINE);

        $this->tombstoneIndex = $this->createMock(TombstoneIndex::class);
    }

    /**
     * @return MockObject|Tombstone
     */
    protected function createTombstone(): MockObject
    {
        return $this->createMock(Tombstone::class);
    }

    protected function stubVampireInscriptionEquals(Tombstone $matchedTombstone, bool $isEqual): void
    {
        $this->vampire
            ->expects($this->any())
            ->method('inscriptionEquals')
            ->with($matchedTombstone)
            ->willReturn($isEqual);
    }

    protected function stubMultipleVampireInscriptionEquals(array $valueMap): void
    {
        $this->vampire
            ->expects($this->any())
            ->method('inscriptionEquals')
            ->willReturnMap($valueMap);
    }
}
