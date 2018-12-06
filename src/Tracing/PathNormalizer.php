<?php

namespace Scheb\Tombstone\Tracing;

class PathNormalizer
{
    public static function normalizeDirectorySeparator(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    public static function makeRelativeTo(string $path, ?string $baseDir): string
    {
        if ($baseDir && self::startsWith($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
            $path = PathNormalizer::normalizeDirectorySeparator($path);
            if ('/' === $path[0]) {
                $path = substr($path, 1);
            }
        }

        return $path;
    }

    private static function startsWith(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strrpos($haystack, $needle, -strlen($haystack));
    }
}
