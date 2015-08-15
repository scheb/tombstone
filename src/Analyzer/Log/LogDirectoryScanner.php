<?php
namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireList;
use SebastianBergmann\FinderFacade\FinderFacade;

class LogDirectoryScanner
{
    /**
     * @var string
     */
    private $logDir;

    /**
     * @var LogReader
     */
    private $logReader;

    /**
     * @param LogReader $logReader
     * @param string $logDir
     */
    public function __construct(LogReader $logReader, $logDir)
    {
        $this->logReader = $logReader;
        $this->logDir = $logDir;
    }

    /**
     * @return VampireList
     */
    public function getVampires()
    {
        $finder = new FinderFacade(array($this->logDir), array(), array('*.log'));
        foreach ($finder->findFiles() as $file) {
            $this->logReader->aggregateLog($file);
        }

        return $this->logReader->getVampires();
    }
}
