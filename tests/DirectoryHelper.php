<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests;

class DirectoryHelper
{
    public static function listDirectory(string $directory): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $relativePathStart = \strlen(realpath($directory)) + 1;
        $files = [];
        foreach ($iterator as $fileInfo) {
            $files[] = str_replace('\\', '/', substr($fileInfo->getRealPath(), $relativePathStart));
        }
        sort($files);

        return $files;
    }

    public static function clearDirectory(string $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileInfo) {
            if ('.gitkeep' === $fileInfo->getBaseName()) {
                continue;
            }
            $cmd = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            @$cmd($fileInfo->getRealPath());
        }
    }
}
