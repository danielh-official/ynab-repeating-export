<?php

use App\Enums\YnabAcceptedFrequency;

test('convert amount from frequency to frequency', function ($amount, $fromFrequency, $toFrequency, $expected) {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            $amount,
            $fromFrequency,
            $toFrequency)
    )->toBe($expected);
})->with([
    '2/month to year equals 24.0' => [2, YnabAcceptedFrequency::monthly, YnabAcceptedFrequency::yearly, 24.0],
    '3/day to year equals 1095.0' => [3, YnabAcceptedFrequency::daily, YnabAcceptedFrequency::yearly, 1095.0],
    '3/year to month equals .25' => [3, YnabAcceptedFrequency::yearly, YnabAcceptedFrequency::monthly, .25],
    '4 per every other week to month equals 8.0' => [4, YnabAcceptedFrequency::everyOtherWeek, YnabAcceptedFrequency::monthly, 8.0],
    '12 per every 3 months to month equals 4.0' => [12, YnabAcceptedFrequency::every3Months, YnabAcceptedFrequency::monthly, 4.0],
    '12 per every 4 months to month equals 3.0' => [12, YnabAcceptedFrequency::every4Months, YnabAcceptedFrequency::monthly, 3.0],
    '3/week to month equals 12.0' => [3, YnabAcceptedFrequency::weekly, YnabAcceptedFrequency::monthly, 12.0],
    '3/week to year equals 156.0' => [3, YnabAcceptedFrequency::weekly, YnabAcceptedFrequency::yearly, 156.0],
    '3 every other week to year equals 78.0' => [3, YnabAcceptedFrequency::everyOtherWeek, YnabAcceptedFrequency::yearly, 78.0],
    '2 twice a month to year equals 48.0' => [2, YnabAcceptedFrequency::twiceAMonth, YnabAcceptedFrequency::yearly, 48.0],
    '1 every 4 weeks to year equals 13.0' => [1, YnabAcceptedFrequency::every4Weeks, YnabAcceptedFrequency::yearly, 13.0],
    '3 every other month to year equals 18.0' => [3, YnabAcceptedFrequency::everyOtherMonth, YnabAcceptedFrequency::yearly, 18.0],
    '3 every 4 months to year equals 9.0' => [3, YnabAcceptedFrequency::every4Months, YnabAcceptedFrequency::yearly, 9.0],
    '3 twice a year to month equals 0.5' => [3, YnabAcceptedFrequency::twiceAYear, YnabAcceptedFrequency::monthly, 0.5],
    '3 twice a year to year equals 1.5' => [3, YnabAcceptedFrequency::twiceAYear, YnabAcceptedFrequency::yearly, 1.5],
    '3 every other year to year equals 1.5' => [3, YnabAcceptedFrequency::everyOtherYear, YnabAcceptedFrequency::yearly, 1.5],
]);
