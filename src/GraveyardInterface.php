<?php

namespace Scheb\Tombstone;

interface GraveyardInterface
{
    public function tombstone(array $arguments, array $trace, array $metadata): void;

    public function flush(): void;
}
