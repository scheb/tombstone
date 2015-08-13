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
            'tombstoneDate' => $vampire->getTombstoneDate(),
            'author' => $vampire->getAuthor(),
            'fileName' => $vampire->getFileName(),
            'line' => $vampire->getLine(),
            'method' => $vampire->getMethod(),
            'invoker' => $vampire->getInvoker(),
        )) . "\n";
    }
}