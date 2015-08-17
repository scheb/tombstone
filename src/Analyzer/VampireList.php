<?php
namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Vampire;

class VampireList implements \Countable, \Iterator
{

    /**
     * @var Vampire[]
     */
    private $vampires = array();

    /**
     * @var int[]
     */
    private $maxDatePerPosition;

    /**
     * @param Vampire $vampire
     */
    public function addVampire(Vampire $vampire)
    {
        $position = $vampire->getPosition();
        $logDate = strtotime($vampire->getAwakeningDate());
        if (!isset($this->vampires[$position]) || $logDate > $this->maxDatePerPosition[$position]) {
            $this->vampires[$position] = $vampire;
            $this->maxDatePerPosition[$position] = $logDate;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->vampires);
    }

    /**
     * @return Vampire
     */
    public function current()
    {
        return current($this->vampires);
    }

    public function next()
    {
        next($this->vampires);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->vampires);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->vampires[$this->key()]);
    }

    public function rewind()
    {
        reset($this->vampires);
    }
}
