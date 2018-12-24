<?php

namespace Scheb\Tombstone\Analyzer\Source;

use Scheb\Tombstone\Analyzer\TombstoneIndex;
use SebastianBergmann\FinderFacade\FinderFacade;

class SourceDirectoryScanner
{
    /**
     * @var TombstoneExtractor
     */
    private $tombstoneExtractor;

    /**
     * @var string[]
     */
    private $files;

    /**
     * @param TombstoneExtractor $tombstoneExtractor
     * @param string             $sourcePath
     * @param array              $regularExpressions Match source files against passed patterns. Defaults to ['*.php']
     */
    public function __construct(TombstoneExtractor $tombstoneExtractor, string $sourcePath, array $regularExpressions = ['*.php'])
    {
        $this->tombstoneExtractor = $tombstoneExtractor;
        $finder = new FinderFacade([$sourcePath], [], $regularExpressions);
        $this->files = $finder->findFiles();
    }

    public function getTombstones(callable $onProgress): TombstoneIndex
    {
        foreach ($this->files as $file) {
            $this->tombstoneExtractor->extractTombstones($file);
            $onProgress();
        }

        return $this->tombstoneExtractor->getTombstones();
    }

    public function getNumFiles(): int
    {
        return count($this->files);
    }
}
