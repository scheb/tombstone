<?php
namespace Scheb\Tombstone\Tracing;

class RelativePath
{
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
