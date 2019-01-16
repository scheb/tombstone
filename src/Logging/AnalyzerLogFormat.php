<?php

namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormat
{
    private const CURRENT_VERSION = 4;

    public static function vampireToLog(Vampire $vampire): string
    {
        return json_encode([
            'v' => self::CURRENT_VERSION,
            'a' => $vampire->getArguments(),
            'f' => $vampire->getFile(),
            'l' => $vampire->getLine(),
            'm' => $vampire->getMethod(),
            'd' => $vampire->getMetadata(),
            's' => self::encodeStackTrace($vampire->getStackTrace()),
            'id' => $vampire->getInvocationDate(),
            'im' => $vampire->getInvoker(),
        ]);
    }

    private static function encodeStackTrace(array $stackTrace): array
    {
        $encodedTrace = [];
        foreach ($stackTrace as $frame) {
            $encodedTrace[] = [
                'f' => $frame['file'],
                'l' => $frame['line'],
                'm' => $frame['function'],
            ];
        }

        return $encodedTrace;
    }

    public static function logToVampire(string $log): ?Vampire
    {
        $data = json_decode($log, true);
        $version = (int) $data['v'] ?? null;

        if ($version === self::CURRENT_VERSION) {
            return new Vampire(
                $data['id'] ?? null,
                $data['im'] ?? null,
                self::decodeStackTrace($data['s'] ?? []),
                new Tombstone(
                    $data['a'] ?? null,
                    $data['f'] ?? 'unknown',
                    $data['l'] ?? 0,
                    $data['m'] ?? null,
                    $data['d'] ?? []
                )
            );
        }

        return null;
    }

    private static function decodeStackTrace(array $stackTrace): array {
        $decodedTrace = [];
        foreach ($stackTrace as $frame) {
            $decodedTrace[] = [
                'file' => $frame['f'],
                'line' => $frame['l'],
                'function' => $frame['m'],
            ];
        }

        return $decodedTrace;
    }
}
