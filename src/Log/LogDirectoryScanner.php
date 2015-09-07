<?php
namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireIndex;
use SebastianBergmann\FinderFacade\FinderFacade;

class LogDirectoryScanner
{
    /**
     * @var LogReader
     */
    private $logReader;

    /**
     * @var string[]
     */
    private $files;

    /**
     * @param LogReader $logReader
     * @param string $logDir
     */
    public function __construct(LogReader $logReader, $logDir)
    {
        $this->logReader = $logReader;
        $finder = new FinderFacade(array($logDir), array(), array('*.tombstone'));
        $this->files = $finder->findFiles();
    }

    /**
     * @param callable $onProgress
     *
     * @return VampireIndex
     */
    public function getVampires(callable $onProgress)
    {
        foreach ($this->files as $file) {
            $this->logReader->aggregateLog($file);
            $onProgress();
        }

        return $this->logReader->getVampires();
    }

    /**
     * @return int
     */
    public function getNumFiles()
    {
        return count($this->files);
    }
}
