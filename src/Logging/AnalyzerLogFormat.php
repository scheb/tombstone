<?php

namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormat
{
    private const CURRENT_VERSION = 3;

    public static function vampireToLog(Vampire $vampire): string
    {
        return json_encode([
            'v' => self::CURRENT_VERSION,
            'a' => $vampire->getArguments(),
            'f' => $vampire->getFile(),
            'n' => $vampire->getLine(),
            'm' => $vampire->getMethod(),
            'd' => $vampire->getMetadata(),
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

        if (self::CURRENT_VERSION === $version) {
            return new Vampire($data['id'], $data['im'], new Tombstone($data['a'], $data['f'], $data['n'], $data['m'], $data['d']));
        }

        return null;
    }
}
