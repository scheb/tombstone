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
        return $vampire->getAwakeningDate() . "\t"
            . $vampire->getInvoker() . "\t"
            . $vampire->getTombstoneDate() . "\t"
            . $vampire->getAuthor() . "\t"
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
        if (count($v) !== 7) {
            return null;
        }

        return new Vampire($v[0], $v[1], new Tombstone($v[2], $v[3], $v[4], $v[5], $v[6]));
    }
}
