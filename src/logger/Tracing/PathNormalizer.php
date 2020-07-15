<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tracing;

class PathNormalizer
{
    private const NORMALIZED_DIRECTORY_SEPARATOR = '/';

    public static function normalizeDirectorySeparator(string $path): string
    {
        return str_replace('\\', self::NORMALIZED_DIRECTORY_SEPARATOR, $path);
    }

    public static function makeRelativeTo(string $path, ?string $baseDir): string
    {
        if (!$baseDir) {
            return $path;
        }

        $normalizedBaseDir = PathNormalizer::normalizeDirectorySeparator($baseDir);
        $normalizedPath = PathNormalizer::normalizeDirectorySeparator($path);
        if (self::startsWith($normalizedPath, $normalizedBaseDir)) {
            $normalizedPath = substr($normalizedPath, strlen($normalizedBaseDir));
            if (self::NORMALIZED_DIRECTORY_SEPARATOR === $normalizedPath[0]) {
                $normalizedPath = substr($normalizedPath, 1);
            }

            return $normalizedPath;
        }

        return $path;
    }

    private static function startsWith(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strrpos($haystack, $needle, -strlen($haystack));
    }
}
