<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Model;

use PHPUnit\Framework\MockObject\MockObject;
use Scheb\Tombstone\Analyzer\Model\TombstoneIndex;
use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Tests\TestCase;

class TombstoneIndexTest extends TestCase
{
    /**
     * @var MockObject|Tombstone
     */
    private $tombstone1;

    /**
     * @var MockObject|Tombstone
     */
    private $tombstone2;

    /**
     * @var MockObject|Tombstone
     */
    private $tombstone3;

    /**
     * @var MockObject|Tombstone
     */
    private $tombstone4;

    /**
     * @var TombstoneIndex
     */
    private $tombstoneIndex;

    protected function setUp(): void
    {
        $this->tombstone1 = $this->createTombstone('file', 1, 'method1');
        $this->tombstone2 = $this->createTombstone('file', 2, 'method1');
        $this->tombstone3 = $this->createTombstone('file', 3, null);
        $this->tombstone4 = $this->createTombstone('file', 4, 'method2');

        $this->tombstoneIndex = new TombstoneIndex();
        $this->tombstoneIndex->addTombstone($this->tombstone1);
        $this->tombstoneIndex->addTombstone($this->tombstone2);
        $this->tombstoneIndex->addTombstone($this->tombstone3);
        $this->tombstoneIndex->addTombstone($this->tombstone4);
    }

    private function createTombstone(string $file, int $line, ?string $method): Tombstone
    {
        $tombstone = $this->createMock(Tombstone::class);
        $tombstone
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($this->createFilePath($file));
        $tombstone
            ->expects($this->any())
            ->method('getLine')
            ->willReturn($line);
        $tombstone
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        return $tombstone;
    }

    private function createFilePath(string $file): FilePathInterface
    {
        $filePath = $this->createMock(FilePathInterface::class);
        $filePath
            ->expects($this->any())
            ->method('getReferencePath')
            ->willReturn($file);

        return $filePath;
    }

    /**
     * @test
     */
    public function count_hasTombstones_returnNumberOfTombstones(): void
    {
        $this->assertEquals(4, $this->tombstoneIndex->count());
    }

    /**
     * @test
     */
    public function getIterator_hasTombstones_iterateAllTombstones(): void
    {
        $tombstones = iterator_to_array($this->tombstoneIndex);
        $this->assertEquals([$this->tombstone1, $this->tombstone2, $this->tombstone3, $this->tombstone4], $tombstones);
    }

    /**
     * @test
     */
    public function getInMethod_hasTombstones_returnArrayOfTombstones(): void
    {
        $returnValue = $this->tombstoneIndex->getInMethod('method1');
        $this->assertCount(2, $returnValue);
        $this->assertContainsOnlyInstancesOf(Tombstone::class, $returnValue);
        $this->assertSame([$this->tombstone1, $this->tombstone2], $returnValue);
    }

    /**
     * @test
     */
    public function getInMethod_noTombstones_returnEmptyArray(): void
    {
        $returnValue = $this->tombstoneIndex->getInMethod('otherMethod');
        $this->assertCount(0, $returnValue);
    }

    /**
     * @test
     */
    public function getInFileAndLine_hasTombstone_returnTombstone(): void
    {
        $returnValue = $this->tombstoneIndex->getInFileAndLine($this->createFilePath('file'), 2);
        $this->assertSame($this->tombstone2, $returnValue);
    }

    /**
     * @test
     */
    public function getInFileAndLine_noTombstone_returnNull(): void
    {
        $returnValue = $this->tombstoneIndex->getInFileAndLine($this->createFilePath('file'), 5);
        $this->assertNull($returnValue);
    }
}
