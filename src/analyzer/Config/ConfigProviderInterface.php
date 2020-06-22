<?php

namespace Scheb\Tombstone\Analyzer\Config;

interface ConfigProviderInterface
{
    public function readConfiguration(): array;
}
