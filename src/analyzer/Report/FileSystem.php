<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Report\Html\HtmlReportException;

class FileSystem
{
    public static function copyDirectoryFiles(string $templateDir, string $reportDir): void
    {
        self::ensureDirectoryCreated($reportDir);
        $handle = opendir($templateDir);
        if (!$handle) {
            throw new HtmlReportException(sprintf('Could not read template files from %s', $templateDir));
        }

        while ($file = readdir($handle)) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $templateFile = self::createPath($templateDir, $file);
            $reportFile = self::createPath($reportDir, $file);

            if (is_dir($templateFile)) {
                self::copyDirectoryFiles($templateFile, $reportFile);
                continue;
            }

            if (!@copy($templateFile, $reportFile)) {
                throw new HtmlReportException(sprintf('Could not copy %s to %s', $templateFile, $reportFile));
            }
        }
        closedir($handle);
    }

    public static function ensureDirectoryCreated(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new HtmlReportException(sprintf('Could not create directory %s', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new HtmlReportException(sprintf('Directory %s has to be writable', $dir));
        }
    }

    public static function createPath(string $parentDirectoryPath, string $name): string
    {
        // Append directory separator if necessary
        if ('' !== $parentDirectoryPath && DIRECTORY_SEPARATOR !== substr($parentDirectoryPath, -1, 1)) {
            $parentDirectoryPath .= DIRECTORY_SEPARATOR;
        }

        return $parentDirectoryPath.$name;
    }
}
