<?php
namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireList;
use Scheb\Tombstone\Logging\LogFormat;
use Scheb\Tombstone\Vampire;

class LogReader
{
    /**
     * @var VampireList
     */
    private $vampires;

    /**
     * @param $vampires
     */
    public function __construct(VampireList $vampires)
    {
        $this->vampires = $vampires;
    }

    /**
     * @return string $file
     *
     * @return Vampire[]
     */
    public function aggregateLog($file)
    {
        $handle = fopen($file, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            $vampire = LogFormat::logToVampire($line);
            if ($vampire) {
                $this->vampires->addVampire($vampire);
            }
        }
        fclose($handle);
    }

    /**
     * @return VampireList
     */
    public function getVampires()
    {
        return $this->vampires;
    }
}
