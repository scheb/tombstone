<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core;

class PathNormalizer
{
    public const NORMALIZED_DIRECTORY_SEPARATOR = '/';

    public static function normalizeDirectorySeparator(string $path): string
    {
        return str_replace('\\', self::NORMALIZED_DIRECTORY_SEPARATOR, $path);
    }

    public static function normalizeDirectorySeparatorForEnvironment(string $path): string
    {
        return strtr($path, [
            '\\' => DIRECTORY_SEPARATOR,
            '/' => DIRECTORY_SEPARATOR,
        ]);
    }
}
