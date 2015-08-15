<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Tombstone;

class TombstoneList implements \Countable, \Iterator
{

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
    private $methodIndex = array();

    /**
     * @param string $file
     * @param string $line
     * @param string $methodName
     * @param string $date
     * @param string $author
     */
    public function addTombstone($file, $line, $methodName, $date, $author)
    {
        $tombstone = new Tombstone($date, $author, $file, $line, $methodName);
        $this->tombstones[] = $tombstone;
        $this->fileLineIndex[$this->getPosition($file, $line)] = $tombstone;
        $this->methodIndex[$methodName] = $tombstone;
    }

    /**
     * @param string $method
     * @return array|null
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
     * @return array|null
     */
    public function getInFileAndLine($file, $line)
    {
        $pos = $this->getPosition($file, $line);
        if ($this->fileLineIndex[$pos]) {
            return $this->fileLineIndex[$pos];
        }

        return null;
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    private function getPosition($file, $line)
    {
        return $file.':'.$line;
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
