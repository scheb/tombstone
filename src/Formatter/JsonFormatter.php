<?php

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class JsonFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return json_encode(array(
            'tombstoneDate' => $vampire->getTombstoneDate(),
            'author' => $vampire->getAuthor(),
            'label' => $vampire->getLabel(),
            'file' => $vampire->getFile(),
            'line' => $vampire->getLine(),
            'method' => $vampire->getMethod(),
            'invocationDate' => $vampire->getInvocationDate(),
            'invoker' => $vampire->getInvoker(),
        ))."\n";
    }
}
