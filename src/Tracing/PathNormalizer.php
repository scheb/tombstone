<?php
namespace Scheb\Tombstone\Tracing;

class PathNormalizer
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function normalizeDirectorySeparator($path) {
        return str_replace('\\', '/', $path);
    }

    /**
     * @param string $path
     * @param string $baseDir
     *
     * @return string
     */
    public static function makeRelativeTo($path, $baseDir)
    {
        if ($baseDir && self::startsWith($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
            $path = PathNormalizer::normalizeDirectorySeparator($path);
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
        }

        return $path;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    private static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
