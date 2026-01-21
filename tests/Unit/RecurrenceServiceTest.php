<?php

namespace Tests\Unit;

use App\Services\RecurrenceService;
use Carbon\Carbon;
use Tests\TestCase;

class RecurrenceServiceTest extends TestCase
{
    protected RecurrenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RecurrenceService();
    }

    /**
     * Test weekly recurrence with 12 occurrences starting 2026-01-19.
     * Expected: First occurrence = 2026-01-19, Last occurrence = 2026-04-06 (11 weeks later)
     */
    public function test_weekly_recurrence_generates_12_occurrences(): void
    {
        $startDate = Carbon::parse('2026-01-19');
        $frequency = 'semanal';
        $interval = 1;
        $totalOccurrences = 12;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        // Assert we get exactly 12 dates
        $this->assertCount(12, $dates);

        // Assert first occurrence is the start date
        $this->assertEquals('2026-01-19', $dates[0]->format('Y-m-d'));

        // Assert last occurrence (12th) is 11 weeks after start (2026-04-06)
        $this->assertEquals('2026-04-06', $dates[11]->format('Y-m-d'));

        // Verify each date is exactly 1 week apart
        for ($i = 1; $i < count($dates); $i++) {
            $expectedDate = $dates[$i - 1]->copy()->addWeeks(1);
            $this->assertEquals($expectedDate->format('Y-m-d'), $dates[$i]->format('Y-m-d'));
        }
    }

    /**
     * Test monthly recurrence with NoOverflow handling.
     * Starting 2026-01-31, interval 1, total 3 occurrences.
     * Expected: 2026-01-31, 2026-02-28 (not 2026-02-31), 2026-03-28 (maintains last valid day)
     */
    public function test_monthly_recurrence_with_overflow_handling(): void
    {
        $startDate = Carbon::parse('2026-01-31');
        $frequency = 'mensal';
        $interval = 1;
        $totalOccurrences = 3;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        // Assert we get exactly 3 dates
        $this->assertCount(3, $dates);

        // Assert first occurrence
        $this->assertEquals('2026-01-31', $dates[0]->format('Y-m-d'));

        // Assert second occurrence (February only has 28 days in 2026)
        // NoOverflow should give us 2026-02-28, not overflow to March
        $this->assertEquals('2026-02-28', $dates[1]->format('Y-m-d'));

        // Assert third occurrence
        // NoOverflow continues from Feb 28, adding 1 month gives Mar 28
        $this->assertEquals('2026-03-28', $dates[2]->format('Y-m-d'));
    }

    /**
     * Test daily recurrence.
     */
    public function test_daily_recurrence(): void
    {
        $startDate = Carbon::parse('2026-01-01');
        $frequency = 'diaria';
        $interval = 1;
        $totalOccurrences = 5;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        $this->assertCount(5, $dates);
        $this->assertEquals('2026-01-01', $dates[0]->format('Y-m-d'));
        $this->assertEquals('2026-01-02', $dates[1]->format('Y-m-d'));
        $this->assertEquals('2026-01-03', $dates[2]->format('Y-m-d'));
        $this->assertEquals('2026-01-04', $dates[3]->format('Y-m-d'));
        $this->assertEquals('2026-01-05', $dates[4]->format('Y-m-d'));
    }

    /**
     * Test yearly recurrence.
     */
    public function test_yearly_recurrence(): void
    {
        $startDate = Carbon::parse('2026-01-15');
        $frequency = 'anual';
        $interval = 1;
        $totalOccurrences = 3;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        $this->assertCount(3, $dates);
        $this->assertEquals('2026-01-15', $dates[0]->format('Y-m-d'));
        $this->assertEquals('2027-01-15', $dates[1]->format('Y-m-d'));
        $this->assertEquals('2028-01-15', $dates[2]->format('Y-m-d'));
    }

    /**
     * Test with interval > 1 (every 2 weeks).
     */
    public function test_bi_weekly_recurrence(): void
    {
        $startDate = Carbon::parse('2026-01-01');
        $frequency = 'semanal';
        $interval = 2; // Every 2 weeks
        $totalOccurrences = 4;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        $this->assertCount(4, $dates);
        $this->assertEquals('2026-01-01', $dates[0]->format('Y-m-d'));
        $this->assertEquals('2026-01-15', $dates[1]->format('Y-m-d'));
        $this->assertEquals('2026-01-29', $dates[2]->format('Y-m-d'));
        $this->assertEquals('2026-02-12', $dates[3]->format('Y-m-d'));
    }

    /**
     * Test edge case: only 1 occurrence.
     */
    public function test_single_occurrence(): void
    {
        $startDate = Carbon::parse('2026-01-01');
        $frequency = 'semanal';
        $interval = 1;
        $totalOccurrences = 1;

        $dates = $this->service->generateOccurrenceDates($startDate, $frequency, $interval, $totalOccurrences);

        $this->assertCount(1, $dates);
        $this->assertEquals('2026-01-01', $dates[0]->format('Y-m-d'));
    }

    /**
     * Test that invalid frequency throws exception.
     */
    public function test_invalid_frequency_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported frequency: invalid');

        $startDate = Carbon::parse('2026-01-01');
        $this->service->generateOccurrenceDates($startDate, 'invalid', 1, 5);
    }
}
