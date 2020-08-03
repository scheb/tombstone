<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use Scheb\Tombstone\Core\Model\FilePathInterface;

interface TombstoneExtractorInterface
{
    public function extractTombstones(FilePathInterface $filePath): void;
}
