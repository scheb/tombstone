<?php
namespace Scheb\Tombstone\Analyzer\Source;

use Scheb\Tombstone\Analyzer\TombstoneList;
use SebastianBergmann\FinderFacade\FinderFacade;

class SourceDirectoryScanner
{
    /**
     * @var TombstoneExtractor
     */
    private $tombstoneExtractor;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @param TombstoneExtractor $tombstoneExtractor
     * @param string $sourcePath
     */
    public function __construct(TombstoneExtractor $tombstoneExtractor, $sourcePath)
    {
        $this->tombstoneExtractor = $tombstoneExtractor;
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return TombstoneList
     */
    public function getTombstones()
    {
        $finder = new FinderFacade(array($this->sourcePath), array(), array('*.php'));
        foreach ($finder->findFiles() as $file) {
            $this->tombstoneExtractor->extractTombstones($file);
        }

        return $this->tombstoneExtractor->getTombstones();
    }
}
