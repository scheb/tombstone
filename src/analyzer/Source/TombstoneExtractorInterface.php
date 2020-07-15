<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

interface TombstoneExtractorInterface
{
    public function extractTombstones(string $filePath): void;
}
