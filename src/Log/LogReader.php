<?php
namespace Scheb\Tombstone\Analyzer\Log;

use Scheb\Tombstone\Analyzer\VampireIndex;
use Scheb\Tombstone\Logging\AnalyzerLogFormat;
use Scheb\Tombstone\Vampire;

class LogReader
{
    /**
     * @var VampireIndex
     */
    private $vampires;

    /**
     * @param $vampires
     */
    public function __construct(VampireIndex $vampires)
    {
        $this->vampires = $vampires;
    }

    /**
     * @param string $file
     *
     * @return Vampire[]
     */
    public function aggregateLog($file)
    {
        $handle = fopen($file, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            $vampire = AnalyzerLogFormat::logToVampire($line);
            if ($vampire) {
                $this->vampires->addVampire($vampire);
            }
        }
        fclose($handle);
    }

    /**
     * @return VampireIndex
     */
    public function getVampires()
    {
        return $this->vampires;
    }
}
