<?php
namespace Scheb\Tombstone\Analyzer\Report;

class PathTools
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolutePath($path) {
        return $path[0] === '/' || preg_match('#^[a-zA-Z]:#', $path);
    }

    /**
     * @param string $path
     * @param string $rootDir
     *
     * @return string
     */
    public static function makePathAbsolute($path, $rootDir) {
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        $directorySeparatorReplacement = array(
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        );
        return $rootDir . DIRECTORY_SEPARATOR . strtr($path, $directorySeparatorReplacement);
    }
}
