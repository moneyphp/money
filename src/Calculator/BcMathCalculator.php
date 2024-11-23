<?php

declare(strict_types=1);

namespace Money\Calculator;

use InvalidArgumentException as CoreInvalidArgumentException;
use Money\Calculator;
use Money\Exception\InvalidArgumentException;
use Money\Money;
use Money\Number;

use function bcadd;
use function bccomp;
use function bcdiv;
use function bcmod;
use function bcmul;
use function bcsub;
use function ltrim;
use function str_contains;

final class BcMathCalculator implements Calculator
{
    private const SCALE = 14;

    /** @phpstan-pure */
    public static function compare(string $a, string $b): int
    {
        return bccomp($a, $b, self::SCALE);
    }

    /** @phpstan-pure */
    public static function add(string $amount, string $addend): string
    {
        $scale = str_contains($amount . $addend, '.') ? self::SCALE : 0;

        return bcadd($amount, $addend, $scale);
    }

    /** @phpstan-pure */
    public static function subtract(string $amount, string $subtrahend): string
    {
        $scale = str_contains($amount . $subtrahend, '.') ? self::SCALE : 0;

        return bcsub($amount, $subtrahend, $scale);
    }

    /** @phpstan-pure */
    public static function multiply(string $amount, string $multiplier): string
    {
        return bcmul($amount, $multiplier, self::SCALE);
    }

    /**
     * @phpstan-pure
     */
    public static function divide(string $amount, string $divisor): string
    {
        if (bccomp($divisor, '0', self::SCALE) === 0) {
            throw InvalidArgumentException::divisionByZero();
        }

        return bcdiv($amount, $divisor, self::SCALE);
    }

    /** @phpstan-pure */
    public static function ceil(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return bcadd($number->__toString(), '0', 0);
        }

        return bcadd($number->__toString(), '1', 0);
    }

    /** @phpstan-pure */
    public static function floor(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return bcadd($number->__toString(), '-1', 0);
        }

        return bcadd($number->__toString(), '0', 0);
    }

    /**
     * @phpstan-pure
     */
    public static function absolute(string $number): string
    {
        return ltrim($number, '-');
    }

    /**
     * @phpstan-param Money::ROUND_* $roundingMode
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function round(string $number, int $roundingMode): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isHalf() === false) {
            return self::roundDigit($number);
        }

        if ($roundingMode === Money::ROUND_HALF_UP) {
            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_DOWN) {
            return bcadd($number->__toString(), '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_EVEN) {
            if ($number->isCurrentEven()) {
                return bcadd($number->__toString(), '0', 0);
            }

            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven()) {
                return bcadd(
                    $number->__toString(),
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd($number->__toString(), '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd($number->__toString(), '0', 0);
            }

            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd(
                    $number->__toString(),
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd(
                $number->__toString(),
                '0',
                0
            );
        }

        throw new CoreInvalidArgumentException('Unknown rounding mode');
    }

    /**
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    private static function roundDigit(Number $number): string
    {
        if ($number->isCloserToNext()) {
            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        return bcadd($number->__toString(), '0', 0);
    }

    /** @phpstan-pure */
    public static function share(string $amount, string $ratio, string $total): string
    {
        return self::floor(bcdiv(bcmul($amount, $ratio, self::SCALE), $total, self::SCALE));
    }

    /**
     * @phpstan-pure
     */
    public static function mod(string $amount, string $divisor): string
    {
        if (bccomp($divisor, '0') === 0) {
            throw InvalidArgumentException::moduloByZero();
        }

        return bcmod($amount, $divisor);
    }
}
