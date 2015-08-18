<?php
namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class LogFormat
{
    /**
     * @param Vampire $vampire
     *
     * @return string
     */
    public static function vampireToLog(Vampire $vampire) {
        return $vampire->getInvocationDate() . "\t"
            . $vampire->getInvoker() . "\t"
            . $vampire->getTombstoneDate() . "\t"
            . $vampire->getAuthor() . "\t"
            . $vampire->getLabel() . "\t"
            . $vampire->getFile() . "\t"
            . $vampire->getLine() . "\t"
            . $vampire->getMethod();
    }

    /***
     * @param string $log
     *
     * @return Vampire|null Returns null when log was not valid
     */
    public static function logToVampire($log)
    {
        $v = explode("\t", trim($log, "\n\r"));
        if (count($v) < 8) {
            return null;
        }

        return new Vampire($v[0], $v[1], new Tombstone($v[2], $v[3] ?: null, $v[4], $v[5], $v[6], $v[7]));
    }
}
