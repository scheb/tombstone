<?php
namespace Scheb\Tombstone\Handlers;

use Scheb\Tombstone\Vampire;

interface HandlerInterface {

    /**
     * Log a vampire
     *
     * @param Vampire $vampire
     */
    public function log(Vampire $vampire);

    /**
     * Flush everything
     */
    public function flush();
}
