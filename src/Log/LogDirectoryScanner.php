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
     * @param string    $logDir
     */
    public function __construct(LogReader $logReader, string $logDir)
    {
        $this->logReader = $logReader;
        $finder = new FinderFacade([$logDir], [], ['*.tombstone']);
        $this->files = $finder->findFiles();
    }

    public function getVampires(callable $onProgress): VampireIndex
    {
        foreach ($this->files as $file) {
            $this->logReader->aggregateLog($file);
            $onProgress();
        }

        return $this->logReader->getVampires();
    }

    public function getNumFiles(): int
    {
        return count($this->files);
    }
}
