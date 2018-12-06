<?php

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class LineFormatter implements FormatterInterface
{
    /**
     * Formats a Vampire for the log.
     *
     * @param Vampire $vampire
     *
     * @return string
     */
    public function format(Vampire $vampire)
    {
        $template = '%s - Vampire detected: tombstone("%s", "%s"%s), in file %s:%s, in function %s, invoked by %s'."\n";

        return sprintf(
            $template,
            $vampire->getInvocationDate(),
            $vampire->getTombstoneDate(),
            $vampire->getAuthor(),
            $vampire->getLabel() ? ', "'.$vampire->getLabel().'"' : '',
            $vampire->getFile(),
            $vampire->getLine(),
            $vampire->getMethod(),
            $vampire->getInvoker()
        );
    }
}
