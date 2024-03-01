<?php

namespace App\Enums;

enum YnabAcceptedFrequency: string
{
    case daily = 'daily';
    case weekly = 'weekly';
    case everyOtherWeek = 'everyOtherWeek';
    case twiceAMonth = 'twiceAMonth';
    case every4Weeks = 'every4Weeks';
    case monthly = 'monthly';
    case everyOtherMonth = 'everyOtherMonth';
    case every3Months = 'every3Months';
    case every4Months = 'every4Months';
    case twiceAYear = 'twiceAYear';
    case yearly = 'yearly';
    case everyOtherYear = 'everyOtherYear';

    /**
     * @param float|int $amount
     * @param YnabAcceptedFrequency $fromFrequency
     * @param YnabAcceptedFrequency $toFrequency
     * @param int $decimalPoints
     * @return float
     */
    public static function convertAmountFromFrequencyToFrequency(float|int $amount, self $fromFrequency, self $toFrequency, int $decimalPoints = 2): float
    {
        $amount = match ($fromFrequency) {
            self::daily => match ($toFrequency) {
                self::weekly => $amount * 7,
                self::monthly => $amount * 30,
                self::yearly => $amount * 365,
                default => $amount,
            },
            self::weekly => match ($toFrequency) {
                self::daily => $amount / 7,
                self::monthly => $amount * 4,
                self::yearly => $amount * 52,
                default => $amount,
            },
            self::monthly => match ($toFrequency) {
                self::daily => $amount / 30,
                self::weekly => $amount / 4,
                self::yearly => $amount * 12,
                default => $amount,
            },
            self::yearly => match ($toFrequency) {
                self::daily => $amount / 365,
                self::weekly => $amount / 52,
                self::monthly => $amount / 12,
                default => $amount,
            },
            self::everyOtherWeek => match ($toFrequency) {
                self::daily => $amount / 14,
                self::weekly => $amount / 2,
                self::monthly => $amount * 2,
                self::yearly => $amount * 26,
                default => $amount,
            },
            self::twiceAMonth => match ($toFrequency) {
                self::daily => $amount / 30,
                self::weekly => $amount / 4,
                self::monthly => $amount / 2,
                self::yearly => $amount * 24,
                default => $amount,
            },
            self::every4Weeks => match ($toFrequency) {
                self::daily => $amount / 28,
                self::weekly => $amount / 4,
                self::monthly => $amount * 4,
                self::yearly => $amount * 13,
                default => $amount,
            },
            self::everyOtherMonth => match ($toFrequency) {
                self::daily => $amount / 60,
                self::weekly => $amount / 8,
                self::monthly => $amount / 2,
                self::yearly => $amount * 6,
                default => $amount,
            },
            self::every3Months => match ($toFrequency) {
                self::daily => $amount / 90,
                self::weekly => $amount / 12,
                self::monthly => $amount / 3,
                self::yearly => $amount * 4,
                default => $amount,
            },
            self::every4Months => match ($toFrequency) {
                self::daily => $amount / 120,
                self::weekly => $amount / 16,
                self::monthly => $amount / 4,
                self::yearly => $amount * 3,
                default => $amount,
            },
            self::twiceAYear => match ($toFrequency) {
                self::daily => $amount / 180,
                self::weekly => $amount / 24,
                self::monthly => $amount / 6,
                self::yearly => $amount / 2,
                default => $amount,
            },
            self::everyOtherYear => match ($toFrequency) {
                self::daily => $amount / 730,
                self::weekly => $amount / 104,
                self::monthly => $amount / 24,
                self::yearly => $amount / 2,
                default => $amount,
            },
        };

        return round($amount, $decimalPoints);
    }
}
