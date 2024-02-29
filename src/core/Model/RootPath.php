<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

use Scheb\Tombstone\Core\PathNormalizer;

class RootPath implements FilePathInterface
{
    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var int
     */
    private $rootPathLength;

    public function __construct(string $rootPath)
    {
        // Use the real rootPath if possible
        if (false !== ($rootDirRealPath = realpath($rootPath))) {
            $rootPath = $rootDirRealPath;
        }

        if (!self::isPathAbsolute($rootPath)) {
            throw new \InvalidArgumentException(sprintf('Root rootPath "%s" must be absolute.', $rootPath));
        }

        $rootPath = PathNormalizer::normalizeDirectorySeparator($rootPath);

        // If missing, append a directory separator at the end
        if (PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR !== substr($rootPath, -1)) {
            $rootPath .= PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR;
        }

        $this->rootPath = $rootPath;
        $this->rootPathLength = \strlen($this->rootPath);
    }

    public function getAbsolutePath(): string
    {
        return $this->rootPath;
    }

    public function getReferencePath(): string
    {
        return $this->rootPath;
    }

    public function createFilePath(string $path): FilePathInterface
    {
        $path = PathNormalizer::normalizeDirectorySeparator($path);

        if (!$this->isPathAbsolute($path)) {
            return $this->createRelativePath($path);
        }

        if ($this->startsWith($path, $this->rootPath)) {
            return $this->createRelativePath(substr($path, $this->rootPathLength));
        }

        return new AbsoluteFilePath($path);
    }

    private function createRelativePath(string $path): RelativeFilePath
    {
        if ('' !== $path && '.' === $path[0]) {
            if ('.' === $path) {
                // Path is equal root path
                return new RelativeFilePath('', $this);
            }
            // Remove leading "./"
            $path = preg_replace('#^(\\./)+#', '', $path);
        }

        return new RelativeFilePath($path, $this);
    }

    private function isPathAbsolute(string $path): bool
    {
        if (!\strlen($path)) {
            return false;
        }

        return '/' === $path[0]
            || '\\' === $path[0]
            || (\strlen($path) >= 3 && preg_match('#^[a-zA-Z]:[/\\\\]#', substr($path, 0, 3)));
    }

    private function startsWith(string $haystack, string $needle): bool
    {
        return $haystack[0] === $needle[0]
            ? 0 === strncmp($haystack, $needle, \strlen($needle))
            : false;
    }
}
