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
            'invocationDate' => $vampire->getInvocationDate(),
            'tombstoneDate' => $vampire->getTombstoneDate(),
            'label' => $vampire->getLabel(),
            'author' => $vampire->getAuthor(),
            'file' => $vampire->getFile(),
            'line' => $vampire->getLine(),
            'method' => $vampire->getMethod(),
            'invoker' => $vampire->getInvoker(),
        )) . "\n";
    }
}
