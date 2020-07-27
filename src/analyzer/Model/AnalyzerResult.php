<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;

class AnalyzerResult extends AbstractResultAggregate
{
    private const INDEX_TYPE_DEAD = 1;
    private const INDEX_TYPE_UNDEAD = 2;
    private const INDEX_TYPE_DELETED = 3;

    /**
     * @var array|null
     */
    private $perFile;

    /**
     * @return AnalyzerFileResult[]
     */
    public function getPerFile(): array
    {
        if (null === $this->perFile) {
            $this->perFile = iterator_to_array($this->createAnalyzerFileResults());
        }

        return $this->perFile;
    }

    private function createAnalyzerFileResults(): \Traversable
    {
        $fileIndex = $this->createFileIndex();
        ksort($fileIndex);
        foreach ($fileIndex as $constructorArgs) {
            yield new AnalyzerFileResult(...$constructorArgs);
        }
    }

    private function createFileIndex(): array
    {
        $fileIndex = [];

        foreach ($this->dead as $tombstone) {
            $this->writeFileIndex($fileIndex, $tombstone, self::INDEX_TYPE_DEAD);
        }
        foreach ($this->undead as $tombstone) {
            $this->writeFileIndex($fileIndex, $tombstone, self::INDEX_TYPE_UNDEAD);
        }
        foreach ($this->deleted as $vampire) {
            $this->writeFileIndex($fileIndex, $vampire, self::INDEX_TYPE_DELETED);
        }

        return $fileIndex;
    }

    /**
     * @param Tombstone|Vampire $item
     */
    private function writeFileIndex(array &$fileIndex, $item, int $indexType): void
    {
        $file = $item->getFile();
        $referencePath = $file->getReferencePath();
        if (!isset($fileIndex[$referencePath])) {
            $fileIndex[$referencePath] = [$file, [], [], []];
        }
        /** @psalm-suppress PossiblyUndefinedMethod */
        $fileIndex[$referencePath][$indexType][] = $item;
    }
}
