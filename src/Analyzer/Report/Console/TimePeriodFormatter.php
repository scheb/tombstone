<?php
namespace Scheb\Tombstone\Analyzer\Report\Console;

class TimePeriodFormatter
{
    const SECONDS_PER_DAY = 86400;

    /**
     * @param string $date
     *
     * @return string
     */
    public static function formatAge($date)
    {
        $tombstoneDate = strtotime($date);
        if (!$tombstoneDate) {
            return '';
        }

        $daysPassed = floor((time() - $tombstoneDate) / self::SECONDS_PER_DAY);
        if ($daysPassed <= 0) {
            return '';
        }

        $weeksPassed = floor($daysPassed / 7);
        $daysPassed = $daysPassed % 7;

        $timePassed = $daysPassed . ' days';
        if ($weeksPassed) {
            $timePassed = $weeksPassed . ' weeks and ' . $timePassed;
        }

        return $timePassed;
    }
}
