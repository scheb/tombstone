<?php

namespace Scheb\Tombstone;

interface GraveyardInterface
{
    public function tombstone(string $date, ?string $author, ?string $label, array $trace): void;

    public function flush(): void;
}
