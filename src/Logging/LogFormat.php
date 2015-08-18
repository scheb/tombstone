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
        return $vampire->getTombstoneDate() . "\t"
            . $vampire->getAuthor() . "\t"
            . $vampire->getLabel() . "\t"
            . $vampire->getFile() . "\t"
            . $vampire->getLine() . "\t"
            . $vampire->getMethod() . "\t"
            . $vampire->getInvocationDate() . "\t"
            . $vampire->getInvoker();

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

        return new Vampire($v[6], $v[7], new Tombstone($v[0], $v[1], $v[2] ?: null, $v[3], $v[4], $v[5]));
    }
}
