<?php

declare(strict_types=1);

namespace Money\Calculator;

use Money\Calculator;

/**
 * Optimized `Money\Calculator` that delegates to either
 * `BcMathCalculator` or `GmpCalculator` depending on the operation.
 *
 * `GmpCalculator` is approx 25% faster for add and subtract.
 * `BcMathCalculator` is faster for all decimal operations.
 *
 * @psalm-immutable
 */
final class PerformanceOptimizedDelegateCalculator implements Calculator
{
    /** @psalm-pure */
    public static function compare(string $a, string $b): int
    {
        return BcMathCalculator::compare($a, $b);
    }

    /** @psalm-pure */
    public static function add(string $amount, string $addend): string
    {
        return GmpCalculator::add($amount, $addend);
    }

    /** @psalm-pure */
    public static function subtract(string $amount, string $subtrahend): string
    {
        return GmpCalculator::subtract($amount, $subtrahend);
    }

    /** @psalm-pure */
    public static function multiply(string $amount, string $multiplier): string
    {
        return BcMathCalculator::multiply($amount, $multiplier);
    }

    /** @psalm-pure */
    public static function divide(string $amount, string $divisor): string
    {
        return BcMathCalculator::divide($amount, $divisor);
    }

    /** @psalm-pure */
    public static function ceil(string $number): string
    {
        return BcMathCalculator::ceil($number);
    }

    /** @psalm-pure */
    public static function floor(string $number): string
    {
        return BcMathCalculator::floor($number);
    }

    /** @psalm-pure */
    public static function absolute(string $number): string
    {
        return BcMathCalculator::absolute($number);
    }

    /**
     * @psalm-pure
     */
    public static function round(string $number, int $roundingMode): string
    {
        return BcMathCalculator::round($number, $roundingMode);
    }

    /** @psalm-pure */
    public static function share(string $amount, string $ratio, string $total): string
    {
        return BcMathCalculator::share($amount, $ratio, $total);
    }

    /** @psalm-pure */
    public static function mod(string $amount, string $divisor): string
    {
        return BcMathCalculator::mod($amount, $divisor);
    }
}
