<?php

namespace Scheb\Tombstone\Analyzer;

class FilePosition
{
    /**
     * @param string $file
     * @param string $line
     *
     * @return string
     */
    public static function createPosition($file, $line)
    {
        return $file.':'.$line;
    }
}
