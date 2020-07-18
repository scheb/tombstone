<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Tracing\PathNormalizer;

class TombstoneIndex implements \Countable, \Iterator
{
    /**
     * @var string|null
     */
    private $rootDir;

    /**
     * @var Tombstone[]
     */
    private $tombstones = [];

    /**
     * @var Tombstone[]
     */
    private $fileLineIndex = [];

    /**
     * @var Tombstone[]
     */
    private $relativeFileLineIndex = [];

    /**
     * @var Tombstone[][]
     */
    private $methodIndex = [];

    public function __construct(?string $rootDir)
    {
        if (null !== $rootDir) {
            $this->rootDir = PathNormalizer::normalizeDirectorySeparator($rootDir);
        }
    }

    public function addTombstone(Tombstone $tombstone): void
    {
        $this->tombstones[] = $tombstone;

        $position = FilePosition::createPosition($tombstone->getFile(), $tombstone->getLine());
        $this->fileLineIndex[$position] = $tombstone;

        $relativePosition = FilePosition::createPosition($this->normalizeAndRelativePath($tombstone->getFile()), $tombstone->getLine());
        $this->relativeFileLineIndex[$relativePosition] = $tombstone;

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
        if (isset($this->methodIndex[$method])) {
            return $this->methodIndex[$method];
        }

        return [];
    }

    public function getInFileAndLine(string $file, int $line): ?Tombstone
    {
        $pos = FilePosition::createPosition($file, $line);
        if (isset($this->fileLineIndex[$pos])) {
            return $this->fileLineIndex[$pos];
        }

        $pos = FilePosition::createPosition($file, $line);
        if (isset($this->relativeFileLineIndex[$pos])) {
            return $this->relativeFileLineIndex[$pos];
        }

        return null;
    }

    private function normalizeAndRelativePath(string $path): string
    {
        $path = PathNormalizer::normalizeDirectorySeparator($path);

        return PathNormalizer::makeRelativeTo($path, $this->rootDir);
    }

    public function count()
    {
        return \count($this->fileLineIndex);
    }

    public function current()
    {
        return current($this->tombstones);
    }

    public function next()
    {
        next($this->tombstones);
    }

    public function key()
    {
        return key($this->tombstones);
    }

    public function valid()
    {
        return isset($this->tombstones[$this->key()]);
    }

    public function rewind()
    {
        reset($this->tombstones);
    }
}
