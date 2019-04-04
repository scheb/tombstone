<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class LineFormatter implements FormatterInterface
{
    public function format(Vampire $vampire): string
    {
        $template = '%s - Vampire detected: %s, in file %s:%s, in function %s, invoked by %s'."\n";

        return sprintf(
            $template,
            $vampire->getInvocationDate(),
            (string) $vampire->getTombstone(),
            $vampire->getFile(),
            $vampire->getLine(),
            $vampire->getMethod(),
            $vampire->getInvoker()
        );
    }
}
