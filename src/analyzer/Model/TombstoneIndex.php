<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\FilePathInterface;
use Scheb\Tombstone\Core\Model\Tombstone;

/**
 * @template-implements \IteratorAggregate<array-key, Tombstone>
 */
class TombstoneIndex implements \Countable, \IteratorAggregate
{
    /**
     * @var Tombstone[]
     */
    private $tombstones = [];

    /**
     * @var Tombstone[]
     */
    private $fileLineIndex = [];

    /**
     * @var Tombstone[][]
     */
    private $methodIndex = [];

    public function addTombstone(Tombstone $tombstone): void
    {
        $this->tombstones[] = $tombstone;

        $position = $this->createPosition($tombstone->getFile()->getReferencePath(), $tombstone->getLine());
        $this->fileLineIndex[$position] = $tombstone;

        $methodName = $tombstone->getMethod();
        if (null !== $methodName) {
            if (!isset($this->methodIndex[$methodName])) {
                $this->methodIndex[$methodName] = [];
            }
            $this->methodIndex[$methodName][] = $tombstone;
        }
    }

    /**
     * @return Tombstone[]
     */
    public function getInMethod(string $method): array
    {
        return $this->methodIndex[$method] ?? [];
    }

    public function getInFileAndLine(FilePathInterface $file, int $line): ?Tombstone
    {
        $pos = $this->createPosition($file->getReferencePath(), $line);

        return $this->fileLineIndex[$pos] ?? null;
    }

    public function count(): int
    {
        return \count($this->tombstones);
    }

    /**
     * @return \Traversable<int, Tombstone>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->tombstones;
    }

    private function createPosition(string $file, int $line): string
    {
        return $file.':'.$line;
    }
}
