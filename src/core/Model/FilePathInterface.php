<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Core\Model;

interface FilePathInterface
{
    public function getAbsolutePath(): string;

    public function getReferencePath(): string;
}
