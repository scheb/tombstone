<?php
namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Vampire;

class LineFormatter implements FormatterInterface
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
        $template = "%s - Vampire detected: %s by %s, in %s:%s in %s, invoked by %s\n";
        return sprintf(
            $template,
            $vampire->getInvocationDate(),
            $vampire->getTombstoneDate(),
            $vampire->getAuthor(),
            $vampire->getFile(),
            $vampire->getLine(),
            $vampire->getMethod(),
            $vampire->getInvoker()
        );
    }
}
