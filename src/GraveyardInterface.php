<?php

namespace Scheb\Tombstone;

interface GraveyardInterface
{
    public function tombstone(array $arguments, array $trace): void;

    public function flush(): void;
}
