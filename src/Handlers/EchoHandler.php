<?php
namespace Scheb\Tombstone\Handlers;

use Scheb\Tombstone\Vampire;

class EchoHandler implements HandlerInterface {

    public function log(Vampire $vampire)
    {
        $template = '%s - Vampire detected: %s by %s, in %s:%s in %s, invoked by %s';
        $msg = sprintf(
            $template,
            $vampire->getAwakeningDate(),
            $vampire->getTombstoneDate(),
            $vampire->getAuthor(),
            $vampire->getFileName(),
            $vampire->getLine(),
            $vampire->getMethod(),
            $vampire->getInvoker()

        );
        echo $msg . PHP_EOL;
    }

    public function flush() {
    }
}
