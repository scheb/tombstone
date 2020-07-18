<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

class FilePosition
{
    public static function createPosition(string $file, int $line): string
    {
        return $file.':'.$line;
    }
}
