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
]);
