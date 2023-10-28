<?php

use App\Enums\YnabAcceptedFrequency;

test('2/month to year equals 24.0', function () {
    $result = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(2, YnabAcceptedFrequency::monthly, YnabAcceptedFrequency::yearly);

    expect($result)->toBe(24.0);
});

test('3/day to year equals 1095.0', function () {
    $result = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(3, YnabAcceptedFrequency::daily, YnabAcceptedFrequency::yearly);

    expect($result)->toBe(1095.0);
});

test('3/year to month equals .25', function () {
    $result = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(3, YnabAcceptedFrequency::yearly, YnabAcceptedFrequency::monthly);

    expect($result)->toBe(.25);
});

test('4 per every other week to month equals 8.0', function () {
    $result = YnabAcceptedFrequency::convertAmountFromFrequencyToFrequency(4, YnabAcceptedFrequency::everyOtherWeek, YnabAcceptedFrequency::monthly);

    expect($result)->toBe(8.0);
});

