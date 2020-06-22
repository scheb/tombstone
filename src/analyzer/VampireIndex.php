<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

use Scheb\Tombstone\Vampire;

class VampireIndex implements \Countable, \Iterator
{
    /**
     * @var Vampire[]
     */
    private $vampires = [];

    /**
     * @var int[]
     */
    private $maxDatePerPosition;

    public function addVampire(Vampire $vampire): void
    {
        $position = FilePosition::createPosition($vampire->getFile(), $vampire->getLine());
        $logDate = strtotime($vampire->getInvocationDate());
        if (!isset($this->vampires[$position]) || $logDate > $this->maxDatePerPosition[$position]) {
            $this->vampires[$position] = $vampire;
            $this->maxDatePerPosition[$position] = $logDate;
        }
    }

    public function count()
    {
        return count($this->vampires);
    }

    public function current()
    {
        return current($this->vampires);
    }

    public function next()
    {
        next($this->vampires);
    }

    public function key()
    {
        return key($this->vampires);
    }

    public function valid()
    {
        return isset($this->vampires[$this->key()]);
    }

    public function rewind()
    {
        reset($this->vampires);
    }
}
