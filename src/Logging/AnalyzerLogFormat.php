<?php

namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormat
{
    private const CURRENT_VERSION = 1;

    public static function vampireToLog(Vampire $vampire): string
    {
        return json_encode([
            'v' => self::CURRENT_VERSION,
            'd' => $vampire->getTombstoneDate(),
            'a' => $vampire->getAuthor(),
            'l' => $vampire->getLabel(),
            'f' => $vampire->getFile(),
            'n' => $vampire->getLine(),
            'm' => $vampire->getMethod(),
            'id' => $vampire->getInvocationDate(),
            'im' => $vampire->getInvoker(),
        ]);
    }

    public static function logToVampire(string $log): ?Vampire
    {
        $data = json_decode($log, true);
        if (null === $data || !isset($data['v'])) {
            return null;
        }
        $version = (int) $data['v'];

        if (1 === $version) {
            return new Vampire($data['id'], $data['im'], new Tombstone($data['d'], $data['a'], $data['l'], $data['f'], $data['n'], $data['m']));
        }

        return null;
    }
}
