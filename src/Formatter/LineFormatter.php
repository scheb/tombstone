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
            $vampire->getAwakeningDate(),
            $vampire->getTombstone()->getTombstoneDate(),
            $vampire->getTombstone()->getAuthor(),
            $vampire->getTombstone()->getFile(),
            $vampire->getTombstone()->getLine(),
            $vampire->getTombstone()->getMethod(),
            $vampire->getInvoker()
        );
    }
}
