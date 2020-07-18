<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Exception;

class AnalyzerLogFormatException extends \Exception
{
    public const INCOMPATIBLE_VERSION = 1;
    public const MISSING_DATA = 2;

    public static function createIncompatibleDataException(int $currentVersion, ?int $version): self
    {
        return new self(
            sprintf('Log data provided in incompatible version, current version %s, provided version: %s', $currentVersion, $version ?? 'unknown'),
            self::INCOMPATIBLE_VERSION
        );
    }

    public static function createMissingDataException(array $missingData): self
    {
        return new self(
            sprintf('Log data is missing fields: %s', implode(', ', $missingData)),
            self::MISSING_DATA
        );
    }
}
