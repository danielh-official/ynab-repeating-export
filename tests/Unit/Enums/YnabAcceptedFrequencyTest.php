<?php

use App\Enums\YnabAcceptedFrequency;

test('2/month to year equals 24.0', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            2,
            YnabAcceptedFrequency::monthly,
            YnabAcceptedFrequency::yearly)
    )->toBe(24.0);
});

test('3/day to year equals 1095.0', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            3,
            YnabAcceptedFrequency::daily,
            YnabAcceptedFrequency::yearly)
    )->toBe(1095.0);
});

test('3/year to month equals .25', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            3,
            YnabAcceptedFrequency::yearly,
            YnabAcceptedFrequency::monthly)
    )->toBe(.25);
});

test('4 per every other week to month equals 8.0', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            4,
            YnabAcceptedFrequency::everyOtherWeek,
            YnabAcceptedFrequency::monthly)
    )->toBe(8.0);
});

test('12 per every 3 months to month equals 4.0', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            12,
            YnabAcceptedFrequency::every3Months,
            YnabAcceptedFrequency::monthly)
    )->toBe(4.0);
});

test('12 per every 4 months to month equals 3.0', function () {
    expect(
        YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(
            12,
            YnabAcceptedFrequency::every4Months,
            YnabAcceptedFrequency::monthly)
    )->toBe(3.0);
});
