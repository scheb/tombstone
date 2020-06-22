<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

class PathTools
{
    public static function makePathAbsolute(string $path, string $rootDir): string
    {
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        // Path is root
        if (!strlen($path) || '.' === $path) {
            return self::normalizeDirectorySeparator($rootDir);
        }

        // Remove leading "./"
        if ('.' === $path[0]) {
            $path = preg_replace('#^(\\.[/\\\\])+#', '', $path);
        }

        return self::normalizeDirectorySeparator($rootDir.($path ? DIRECTORY_SEPARATOR.$path : ''));
    }

    private static function normalizeDirectorySeparator(string $path): string
    {
        return strtr($path, [
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ]);
    }

    private static function isAbsolutePath(string $path): bool
    {
        if (!strlen($path)) {
            return false;
        }

        return '/' === $path[0]
            || '\\' === $path[0]
            || (strlen($path) >= 3 && preg_match('#^[a-zA-Z]:[/\\\\]#', substr($path, 0, 3)));
    }
}
