<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

use Scheb\Tombstone\Core\PathNormalizer;

class AbsoluteFilePath implements FilePathInterface
{
    /**
     * @var string
     */
    private $absolutePath;

    public function __construct(string $absolutePath)
    {
        $this->absolutePath = PathNormalizer::normalizeDirectorySeparator($absolutePath);
    }

    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }

    public function getReferencePath(): string
    {
        return $this->absolutePath;
    }
}
