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
     * @param string $sourcePath
     */
    public function __construct(TombstoneExtractor $tombstoneExtractor, $sourcePath)
    {
        $this->tombstoneExtractor = $tombstoneExtractor;
        $finder = new FinderFacade(array($sourcePath), array(), array('*.php'));
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
