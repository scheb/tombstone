<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Logging;

use Scheb\Tombstone\StackTraceFrame;
use Scheb\Tombstone\Tombstone;
use Scheb\Tombstone\Vampire;

class AnalyzerLogFormat
{
    private const F_VERSION = 'v';
    private const F_ARGUMENTS = 'a';
    private const F_FILE = 'f';
    private const F_LINE = 'l';
    private const F_METHOD = 'm';
    private const F_METADATA = 'd';
    private const F_STACKTRACE = 's';
    private const F_INVOCATION_DATE = 'id';
    private const F_INVOKER = 'im';

    private const CURRENT_VERSION = 4;
    private const FILE_DEFAULT_VALUE = 'unknown';

    public static function vampireToLog(Vampire $vampire): string
    {
        return json_encode([
            self::F_VERSION => self::CURRENT_VERSION,
            self::F_ARGUMENTS => $vampire->getArguments(),
            self::F_FILE => $vampire->getFile(),
            self::F_LINE => $vampire->getLine(),
            self::F_METHOD => $vampire->getMethod(),
            self::F_METADATA => $vampire->getMetadata(),
            self::F_STACKTRACE => self::encodeStackTrace($vampire->getStackTrace()),
            self::F_INVOCATION_DATE => $vampire->getInvocationDate(),
            self::F_INVOKER => $vampire->getInvoker(),
        ]);
    }

    private static function encodeStackTrace(array $stackTrace): array
    {
        $encodedTrace = [];
        foreach ($stackTrace as $frame) {
            $encodedTrace[] = [
                self::F_FILE => $frame->getFile(),
                self::F_LINE => $frame->getLine(),
                self::F_METHOD => $frame->getMethod(),
            ];
        }

        return $encodedTrace;
    }

    public static function logToVampire(string $log): ?Vampire
    {
        $data = json_decode($log, true);
        $version = (int) $data[self::F_VERSION] ?? null;

        if (self::CURRENT_VERSION === $version) {
            return new Vampire(
                $data[self::F_INVOCATION_DATE] ?? null,
                $data[self::F_INVOKER] ?? null,
                self::decodeStackTrace($data[self::F_STACKTRACE] ?? []),
                new Tombstone(
                    $data[self::F_ARGUMENTS] ?? [],
                    $data[self::F_FILE] ?? self::FILE_DEFAULT_VALUE,
                    $data[self::F_LINE] ?? 0,
                    $data[self::F_METHOD] ?? null,
                    $data[self::F_METADATA] ?? []
                )
            );
        }

        return null;
    }

    private static function decodeStackTrace(array $stackTrace): array
    {
        $decodedTrace = [];
        foreach ($stackTrace as $frame) {
            $decodedTrace[] = new StackTraceFrame(
                $frame[self::F_FILE] ?? null,
                $frame[self::F_LINE] ?? null,
                $frame[self::F_METHOD] ?? null
            );
        }

        return $decodedTrace;
    }
}
