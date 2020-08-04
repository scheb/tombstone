<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Model;

use Scheb\Tombstone\Core\Model\RelativeFilePath;
use Scheb\Tombstone\Core\Model\Tombstone;
use Scheb\Tombstone\Core\Model\Vampire;
use Scheb\Tombstone\Core\PathNormalizer;

class AnalyzerResult extends AbstractResultAggregate implements \Serializable
{
    private const INDEX_TYPE_DEAD = 1;
    private const INDEX_TYPE_UNDEAD = 2;
    private const INDEX_TYPE_DELETED = 3;

    /**
     * @var array|null
     */
    private $perFile;

    /**
     * @var AnalyzerDirectoryResult|null
     */
    private $rootDirectoryResult;

    /**
     * @param Tombstone[] $dead
     * @param Tombstone[] $undead
     * @param Vampire[] $deleted
     */
    public function __construct(array $dead, array $undead, array $deleted)
    {
        parent::__construct($dead, $undead, $deleted);
    }

    /**
     * @return AnalyzerFileResult[]
     */
    public function getFileResults(): array
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

    public function getRootDirectoryResult(): AnalyzerDirectoryResult
    {
        if (null === $this->rootDirectoryResult) {
            $this->rootDirectoryResult = $this->createRootDirectoryResult();
        }

        return $this->rootDirectoryResult;
    }

    private function createRootDirectoryResult(): AnalyzerDirectoryResult
    {
        $tree = ['dirs' => [], 'files' => []];
        foreach ($this->getFileResults() as $fileResult) {
            $file = $fileResult->getFile();
            if ($file instanceof RelativeFilePath) {
                $pathSegments = explode(PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR, $file->getRelativePath());
                $this->writeResultDirectoryTree($tree, $pathSegments, $fileResult);
            }
        }

        return $this->createAnalyzerDirectoryResult('', $tree);
    }

    private function writeResultDirectoryTree(array &$tree, array $pathSegments, AnalyzerFileResult $fileResult): void
    {
        if (1 === \count($pathSegments)) {
            $tree['files'][] = $fileResult;
        } else {
            $pathPart = array_shift($pathSegments);
            if (!isset($tree['dirs'][$pathPart])) {
                $tree['dirs'][$pathPart] = ['dirs' => [], 'files' => []];
            }
            $this->writeResultDirectoryTree($tree['dirs'][$pathPart], $pathSegments, $fileResult);
        }
    }

    private function createAnalyzerDirectoryResult(string $directoryPath, array $directoryContent): AnalyzerDirectoryResult
    {
        $directoryResults = [];
        foreach ($directoryContent['dirs'] as $subDirectoryName => $subDirectoryContent) {
            $subDirectoryPath = $this->createSubDirectoryPath($directoryPath, $subDirectoryName);
            $directoryResults[] = $this->createAnalyzerDirectoryResult($subDirectoryPath, $subDirectoryContent);
        }

        return new AnalyzerDirectoryResult($directoryPath, $directoryResults, $directoryContent['files']);
    }

    private function createSubDirectoryPath(string $parentDirectoryPath, string $directoryName): string
    {
        // Append directory separator if necessary
        if ('' !== $parentDirectoryPath && PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR !== substr($parentDirectoryPath, -1, 1)) {
            $parentDirectoryPath .= PathNormalizer::NORMALIZED_DIRECTORY_SEPARATOR;
        }

        return $parentDirectoryPath.$directoryName;
    }

    public function __serialize(): array
    {
        return [
            $this->dead,
            $this->undead,
            $this->deleted,
        ];
    }

    // Compatibility PHP < 7.4
    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        [
            $this->dead,
            $this->undead,
            $this->deleted,
        ] = $data;
    }

    // Compatibility PHP < 7.4
    public function unserialize($serialized): void
    {
        $this->__unserialize(unserialize($serialized));
    }
}
