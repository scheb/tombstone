<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Tests\Analyzer\Report;

use Scheb\Tombstone\Analyzer\Report\TimePeriodFormatter;
use Scheb\Tombstone\Tests\TestCase;

class TimePeriodFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function formatAge_invalidDateString_returnNull(): void
    {
        $this->assertNull(TimePeriodFormatter::formatAge('invalid'));
    }

    /**
     * @test
     * @dataProvider getFormatDateTestCases
     */
    public function formatAge_dateGiven_returnFormattedTimePeriod(string $date, string $expectedFormattedPeriod): void
    {
        $returnValue = TimePeriodFormatter::formatAge($date);
        $this->assertEquals($expectedFormattedPeriod, $returnValue);
    }

    public function getFormatDateTestCases(): array
    {
        $fewHoursDate = date('Y-m-d', strtotime('-6 hours'));
        $daysDate = date('Y-m-d', strtotime('-5 days'));
        $weeksDate = date('Y-m-d', strtotime('-18 days'));

        return [
            'hours' => [$fewHoursDate, 'less than a day'],
            'days' => [$daysDate, '5 days'],
            'weeks' => [$weeksDate, '2 weeks, 4 days'],
        ];
    }
}
