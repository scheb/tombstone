<?php
namespace Scheb\Tombstone\Handler;

use Scheb\Tombstone\Vampire;

class EchoHandler extends AbstractHandler {

    /**
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire)
    {
        echo $this->getFormatter()->format($vampire) . PHP_EOL;
    }
}
