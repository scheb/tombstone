<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer;

class PathTools
{
    private const NORMALIZED_DIRECTORY_SEPARATOR = '/';

    public static function makeRelativeTo(string $path, string $baseDir): string
    {
        $normalizedBaseDir = self::normalizeDirectorySeparator($baseDir);
        $normalizedPath = self::normalizeDirectorySeparator($path);
        if (self::startsWith($normalizedPath, $normalizedBaseDir)) {
            $normalizedPath = substr($normalizedPath, \strlen($normalizedBaseDir));

            // Remove any leading directory separators
            if (self::NORMALIZED_DIRECTORY_SEPARATOR === $normalizedPath[0]) {
                $normalizedPath = substr($normalizedPath, 1);
            }

            return $normalizedPath;
        }

        return $path;
    }

    public static function makePathAbsolute(string $path, string $rootDir): string
    {
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        // Path is root
        if (!\strlen($path) || '.' === $path) {
            return self::normalizeDirectorySeparator($rootDir);
        }

        // Remove leading "./"
        if ('.' === $path[0]) {
            $path = preg_replace('#^(\\.[/\\\\])+#', '', $path);
        }

        return self::normalizeDirectorySeparator($rootDir.($path ? DIRECTORY_SEPARATOR.$path : ''));
    }

    public static function normalizeDirectorySeparator(string $path): string
    {
        return strtr($path, [
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ]);
    }

    private static function isAbsolutePath(string $path): bool
    {
        if (!\strlen($path)) {
            return false;
        }

        return '/' === $path[0]
            || '\\' === $path[0]
            || (\strlen($path) >= 3 && preg_match('#^[a-zA-Z]:[/\\\\]#', substr($path, 0, 3)));
    }

    private static function startsWith(string $haystack, string $needle): bool
    {
        return $haystack[0] === $needle[0]
            ? 0 === strncmp($haystack, $needle, \strlen($needle))
            : false;
    }
}
