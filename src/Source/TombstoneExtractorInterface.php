<?php

namespace Scheb\Tombstone\Analyzer\Source;

interface TombstoneExtractorInterface
{
    public function extractTombstones(string $filePath): void;
}
