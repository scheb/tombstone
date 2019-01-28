<?php

namespace Scheb\Tombstone\Analyzer\Log;

interface LogReaderInterface
{
    public function collectVampires(): void;
}
