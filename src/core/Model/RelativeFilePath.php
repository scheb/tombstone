<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

use Scheb\Tombstone\Core\PathNormalizer;

class RelativeFilePath extends AbsoluteFilePath
{
    /**
     * @var string
     */
    private $relativePath;

    public function __construct(string $path, RootPath $rootPath)
    {
        $path = PathNormalizer::normalizeDirectorySeparator($path);

        $this->relativePath = $path;
        parent::__construct($rootPath->getAbsolutePath().$this->relativePath);
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function getReferencePath(): string
    {
        return $this->relativePath;
    }
}
