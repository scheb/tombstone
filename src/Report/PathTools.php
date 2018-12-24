<?php

namespace Scheb\Tombstone\Analyzer\Report;

class PathTools
{
    public static function isAbsolutePath(string $path): bool
    {
        return $path && ('/' === $path[0] || preg_match('#^[a-zA-Z]:#', $path));
    }

    public static function makePathAbsolute(string $path, string $rootDir): string
    {
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        $directorySeparatorReplacement = array(
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        );

        return $rootDir.($path ? DIRECTORY_SEPARATOR.strtr($path, $directorySeparatorReplacement) : '');
    }
}
