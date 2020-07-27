<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\FilePathInterface;

class AnalyzerFileResult extends AbstractResultAggregate
{
    /**
     * @var FilePathInterface
     */
    private $file;

    public function __construct(FilePathInterface $file, array $dead, array $undead, array $deleted)
    {
        parent::__construct($dead, $undead, $deleted);
        $this->file = $file;
    }

    public function getFile(): FilePathInterface
    {
        return $this->file;
    }
}
