<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

class StackTrace implements \Countable, StackTraceInterface
{
    /**
     * @var StackTraceFrame[]
     */
    private $frames;

    public function __construct(StackTraceFrame ...$frames)
    {
        $this->frames = $frames;
    }

    public function getHash(): int
    {
        $frameHashes = [];
        foreach ($this->frames as $frame) {
            $frameHashes[] = $frame->getHash();
        }

        return crc32(implode("\n", $frameHashes));
    }

    public function count(): int
    {
        return \count($this->frames);
    }

    /**
     * @return \Traversable<array-key, StackTraceFrame>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->frames;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->frames[$offset]);
    }

    public function offsetGet($offset): StackTraceFrame
    {
        return $this->frames[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException('StackTrace is read only.');
    }

    public function offsetUnset($offset): void
    {
        throw new \RuntimeException('StackTrace is read only.');
    }
}
