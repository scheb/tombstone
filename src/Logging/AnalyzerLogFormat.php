<?php
namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormat
{
    const CURRENT_VERSION = 1;

    /**
     * @param Vampire $vampire
     *
     * @return string
     */
    public static function vampireToLog(Vampire $vampire) {
        return json_encode(array(
            'v' => self::CURRENT_VERSION,
            'd' => $vampire->getTombstoneDate(),
            'a' => $vampire->getAuthor(),
            'l' => $vampire->getLabel(),
            'f' => $vampire->getFile(),
            'n' => $vampire->getLine(),
            'm' => $vampire->getMethod(),
            'id' => $vampire->getInvocationDate(),
            'im' => $vampire->getInvoker(),
        ));
    }

    /***
     * @param string $log
     *
     * @return Vampire|null Returns null when log was not valid
     */
    public static function logToVampire($log)
    {
        $data = json_decode($log, true);
        if ($data === null || !isset($data['v'])) {
            return null;
        }
        $version = (int) $data['v'];

        if ($version === 1) {
            return new Vampire($data['id'], $data['im'], new Tombstone($data['d'], $data['a'], $data['l'], $data['f'], $data['n'], $data['m']));
        }

        return null;
    }
}
