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
    public function __construct(TombstoneExtractor $tombstoneExtractor, $sourcePath, $regularExpressions = array('*.php'))
    {
        $this->tombstoneExtractor = $tombstoneExtractor;
        $finder = new FinderFacade(array($sourcePath), array(), $regularExpressions);
        $this->files = $finder->findFiles();
    }

    /**
     * @param callable $onProgress
     *
     * @return TombstoneIndex
     */
    public function getTombstones(callable $onProgress)
    {
        foreach ($this->files as $file) {
            $this->tombstoneExtractor->extractTombstones($file);
            $onProgress();
        }

        return $this->tombstoneExtractor->getTombstones();
    }

    /**
     * @return int
     */
    public function getNumFiles()
    {
        return count($this->files);
    }
}
