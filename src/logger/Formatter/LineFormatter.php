<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class LineFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        $line = sprintf('Vampire detected: %s, in file %s:%s', (string) $vampire->getTombstone(), $vampire->getFile(), $vampire->getLine());

        if (null !== $date = $vampire->getInvocationDate()) {
            $line = $date.' - '.$line;
        }

        if (null !== $method = $vampire->getMethod()) {
            $line .= ', in function '.$method;
        }
        if (null !== $invoker = $vampire->getInvoker()) {
            $line .= ', invoked by '.$invoker;
        }

        return $line.PHP_EOL;
    }
}
