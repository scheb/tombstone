<?php

namespace Scheb\Tombstone;

class BufferedGraveyard implements GraveyardInterface
{
    /**
     * @var GraveyardInterface
     */
    private $graveyard;

    /**
     * @var array
     */
    private $tombstoneCalls = [];

    public function __construct(GraveyardInterface $graveyard)
    {
        $this->graveyard = $graveyard;
    }

    public function tombstone(array $arguments, array $trace, array $metadata): void
    {
        $this->tombstoneCalls[] = func_get_args();
    }

    public function flush(): void
    {
        foreach ($this->tombstoneCalls as $args) {
            $this->graveyard->tombstone(...$args);
        }
        $this->tombstoneCalls = [];
        $this->graveyard->flush();
    }
}
