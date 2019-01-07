<?php

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class JsonFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        return json_encode([
            'arguments' => $vampire->getArguments(),
            'file' => $vampire->getFile(),
            'line' => $vampire->getLine(),
            'method' => $vampire->getMethod(),
            'invocationDate' => $vampire->getInvocationDate(),
            'invoker' => $vampire->getInvoker(),
            ])."\n";
    }
}
