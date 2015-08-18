<?php
namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class LogFormat
{
    const CURRENT_VERSION = 1;

    /**
     * @param Vampire $vampire
     *
     * @return string
     */
    public static function vampireToLog(Vampire $vampire) {
        return self::CURRENT_VERSION . "\t"
            . $vampire->getTombstoneDate() . "\t"
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
        $version = isset($v[0]) ? (int) $v[0] : null;

        if ($version === 1) {
            return new Vampire($v[7], $v[8], new Tombstone($v[1], $v[2], $v[3] ?: null, $v[4], $v[5], $v[6]));
        }

        return null;
    }
}
