<?php
namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class JsonFormatter implements FormatterInterface
{

    /**
     * Formats a Vampire for the log
     *
     * @param Vampire $vampire
     *
     * @return string
     */
    public function format(Vampire $vampire)
    {
        return json_encode(array(
            'awakeningDate' => $vampire->getAwakeningDate(),
            'tombstoneDate' => $vampire->getTombstone()->getTombstoneDate(),
            'author' => $vampire->getTombstone()->getAuthor(),
            'file' => $vampire->getTombstone()->getFile(),
            'line' => $vampire->getTombstone()->getLine(),
            'method' => $vampire->getTombstone()->getMethod(),
            'invoker' => $vampire->getInvoker(),
        )) . "\n";
    }
}
