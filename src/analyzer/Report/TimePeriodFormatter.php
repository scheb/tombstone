<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Report;

class TimePeriodFormatter
{
    private const SECONDS_PER_DAY = 86400;

    public static function formatAge(string $date): ?string
    {
        $tombstoneDate = strtotime($date);
        if (false === $tombstoneDate) {
            return null;
        }

        $daysPassed = floor((time() - $tombstoneDate) / self::SECONDS_PER_DAY);
        if ($daysPassed <= 0) {
            return 'less than a day';
        }

        $weeksPassed = floor($daysPassed / 7);
        $daysPassed = $daysPassed % 7;

        $timePassed = $daysPassed.' days';
        if ($weeksPassed) {
            $timePassed = $weeksPassed.' weeks, '.$timePassed;
        }

        return $timePassed;
    }
}
