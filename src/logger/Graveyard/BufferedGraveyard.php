<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logger\Graveyard;

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

    /**
     * @var bool
     */
    private $autoFlush = false;

    public function __construct(GraveyardInterface $graveyard)
    {
        $this->graveyard = $graveyard;
    }

    public function setAutoFlush(bool $autoFlush): void
    {
        $this->autoFlush = $autoFlush;
    }

    public function logTombstoneCall(array $arguments, array $trace, array $metadata): void
    {
        if ($this->autoFlush) {
            $this->graveyard->logTombstoneCall($arguments, $trace, $metadata);
        } else {
            $this->tombstoneCalls[] = \func_get_args();
        }
    }

    public function flush(): void
    {
        foreach ($this->tombstoneCalls as $args) {
            $this->graveyard->logTombstoneCall(...$args);
        }
        $this->tombstoneCalls = [];
        $this->graveyard->flush();
    }
}
