<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Config;

interface ConfigProviderInterface
{
    public function readConfiguration(): array;
}
