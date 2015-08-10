<?php
namespace Scheb\Tombstone\Formatters;

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
            $vampire->getTombstoneDate(),
            $vampire->getAuthor(),
            $vampire->getFileName(),
            $vampire->getLine(),
            $vampire->getMethod(),
            $vampire->getInvoker()
        );
    }
}
