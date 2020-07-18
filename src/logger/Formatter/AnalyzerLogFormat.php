<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Formatter;

use Scheb\Tombstone\Exception\AnalyzerLogFormatException;
use Scheb\Tombstone\Model\StackTraceFrame;
use Scheb\Tombstone\Model\Tombstone;
use Scheb\Tombstone\Model\Vampire;

class AnalyzerLogFormat
{
    private const FIELD_VERSION = 'v';
    private const FIELD_ARGUMENTS = 'a';
    private const FIELD_FILE = 'f';
    private const FIELD_LINE = 'l';
    private const FIELD_METHOD = 'm';
    private const FIELD_METADATA = 'd';
    private const FIELD_STACKTRACE = 's';
    private const FIELD_INVOCATION_DATE = 'id';
    private const FIELD_INVOKER = 'im';

    private const CURRENT_VERSION = 10000; // 1.0.0

    public static function vampireToLog(Vampire $vampire): string
    {
        return json_encode([
            self::FIELD_VERSION => self::CURRENT_VERSION,
            self::FIELD_ARGUMENTS => $vampire->getArguments(),
            self::FIELD_FILE => $vampire->getFile(),
            self::FIELD_LINE => $vampire->getLine(),
            self::FIELD_METHOD => $vampire->getMethod(),
            self::FIELD_METADATA => $vampire->getMetadata(),
            self::FIELD_STACKTRACE => self::encodeStackTrace($vampire->getStackTrace()),
            self::FIELD_INVOCATION_DATE => $vampire->getInvocationDate(),
            self::FIELD_INVOKER => $vampire->getInvoker(),
        ]);
    }

    private static function encodeStackTrace(array $stackTrace): array
    {
        $encodedTrace = [];
        foreach ($stackTrace as $frame) {
            $encodedTrace[] = [
                self::FIELD_FILE => $frame->getFile(),
                self::FIELD_LINE => $frame->getLine(),
                self::FIELD_METHOD => $frame->getMethod(),
            ];
        }

        return $encodedTrace;
    }

    public static function logToVampire(string $log): ?Vampire
    {
        $data = json_decode($log, true);
        if (!\is_array($data)) {
            return null;
        }

        $version = isset($data[self::FIELD_VERSION]) ? (int) $data[self::FIELD_VERSION] : null;

        if (self::CURRENT_VERSION !== $version) {
            throw AnalyzerLogFormatException::createIncompatibleDataException(self::CURRENT_VERSION, $version);
        }

        $requiredFields = [
            self::FIELD_INVOCATION_DATE,
            self::FIELD_ARGUMENTS,
            self::FIELD_FILE,
            self::FIELD_LINE,
        ];

        if ($missingData = array_diff($requiredFields, array_keys($data))) {
            throw AnalyzerLogFormatException::createMissingDataException($missingData);
        }

        return new Vampire(
            $data[self::FIELD_INVOCATION_DATE],
            $data[self::FIELD_INVOKER] ?? null,
            self::decodeStackTrace($data[self::FIELD_STACKTRACE] ?? []),
            new Tombstone(
                $data[self::FIELD_ARGUMENTS],
                $data[self::FIELD_FILE],
                $data[self::FIELD_LINE],
                $data[self::FIELD_METHOD] ?? null,
                $data[self::FIELD_METADATA] ?? []
            )
        );
    }

    private static function decodeStackTrace(array $stackTrace): array
    {
        $requiredFields = [
            self::FIELD_FILE,
            self::FIELD_LINE,
        ];

        $decodedTrace = [];
        foreach ($stackTrace as $frame) {
            if ($missingData = array_diff($requiredFields, array_keys($frame))) {
                break; // Stack trace is incomplete, gracefully truncate at this point
            }

            $decodedTrace[] = new StackTraceFrame(
                $frame[self::FIELD_FILE],
                $frame[self::FIELD_LINE],
                $frame[self::FIELD_METHOD] ?? null
            );
        }

        return $decodedTrace;
    }
}
