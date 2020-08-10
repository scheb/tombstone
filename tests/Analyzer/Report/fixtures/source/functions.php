<?php

declare(strict_types=1);

function globalFunction(): void
{
    tombstone('2020-01-01', 'globalFunction');
}

tombstone('2020-01-01', 'globalScope');
