<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\Vampire;

/**
 * @template-implements \IteratorAggregate<array-key, Vampire>
 */
class VampireIndex implements \Countable, \IteratorAggregate
{
    /**
     * @var Vampire[]
     */
    private $vampiresByHash = [];

    public function addVampire(Vampire $vampire): void
    {
        $hash = $vampire->getHash();

        // Deduplicate vampires by hash, prefer more recent ones
        if (isset($this->vampiresByHash[$hash])) {
            $this->vampiresByHash[$hash] = $this->chooseMoreRecent($this->vampiresByHash[$hash], $vampire);
        } else {
            $this->vampiresByHash[$hash] = $vampire;
        }
    }

    private function chooseMoreRecent(Vampire $vampire1, Vampire $vampire2): Vampire
    {
        return $this->getInvocationDate($vampire1) > $this->getInvocationDate($vampire2) ? $vampire1 : $vampire2;
    }

    private function getInvocationDate(Vampire $vampire): int
    {
        $time = strtotime($vampire->getInvocationDate());

        return false !== $time ? $time : 0;
    }

    public function count(): int
    {
        return \count($this->vampiresByHash);
    }

    /**
     * @return \Traversable<int, Vampire>
     */
    public function getIterator(): \Traversable
    {
        // Doing this to remove array keys (hashes)
        foreach ($this->vampiresByHash as $vampire) {
            yield $vampire;
        }
    }
}
