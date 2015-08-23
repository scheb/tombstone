<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Tracing\PathNormalizer;

class TombstoneIndex implements \Countable, \Iterator
{
    /**
     * @var string
     */
    private $sourceDir;

    /**
     * @var Tombstone[]
     */
    private $tombstones = array();

    /**
     * @var Tombstone[]
     */
    private $fileLineIndex = array();

    /**
     * @var Tombstone[]
     */
    private $relativeFileLineIndex = array();

    /**
     * @var Tombstone[][]
     */
    private $methodIndex = array();

    /**
     * @param string $sourceDir
     */
    public function __construct($sourceDir)
    {
        $this->sourceDir = PathNormalizer::normalizeDirectorySeparator($sourceDir);
    }

    /**
     * @param Tombstone $tombstone
     */
    public function addTombstone(Tombstone $tombstone)
    {
        $this->tombstones[] = $tombstone;

        $position = FilePosition::createPosition($tombstone->getFile(), $tombstone->getLine());
        $this->fileLineIndex[$position] = $tombstone;

        $relativePosition = FilePosition::createPosition($this->normalizeAndRelativePath($tombstone->getFile()), $tombstone->getLine());
        $this->relativeFileLineIndex[$relativePosition] = $tombstone;

        $methodName = $tombstone->getMethod();
        if (!isset($this->methodIndex[$methodName])) {
            $this->methodIndex[$methodName] = array();
        }
        $this->methodIndex[$methodName][] = $tombstone;
    }

    /**
     * @param string $method
     * @return Tombstone[]
     */
    public function getInMethod($method)
    {
        if (isset($this->methodIndex[$method])) {
            return $this->methodIndex[$method];
        }

        return null;
    }

    /**
     * @param string $file
     * @param int $line
     * @return Tombstone
     */
    public function getInFileAndLine($file, $line)
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

    /**
     * @param string $path
     *
     * @return string
     */
    private function normalizeAndRelativePath($path)
    {
        $path = PathNormalizer::normalizeDirectorySeparator($path);
        return PathNormalizer::makeRelativeTo($path, $this->sourceDir);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->fileLineIndex);
    }

    /**
     * @return Tombstone
     */
    public function current()
    {
        return current($this->tombstones);
    }

    public function next()
    {
        next($this->tombstones);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->tombstones);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->tombstones[$this->key()]);
    }

    public function rewind()
    {
        reset($this->tombstones);
    }
}
