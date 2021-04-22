<?php

declare(strict_types=1);

namespace Money\Calculator;

use Money\Calculator;
use Money\Exception\InvalidArgumentException;
use Money\Money;
use Money\Number;
use OverflowException;
use UnderflowException;
use UnexpectedValueException;

use function ceil;
use function filter_var;
use function floor;
use function ltrim;
use function round;

use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;

/**
 * @internal  this calculator is **NOT** supposed to be used in a production environment. You
 *            are generally supposed to use the {@see BcMathCalculator} or the {@see GmpCalculator},
 *            but this class can function if your environment cannot operate with either. Beware
 *            that by using this class you are supposed to be fully aware that fixed point arithmetic
 *            is likely going to fail you.
 *
 * @psalm-immutable
 */
final class PhpCalculator implements Calculator
{
    /** @psalm-pure */
    public static function compare(string $a, string $b): int
    {
        return $a <=> $b;
    }

    /** @psalm-pure */
    public static function add(string $amount, string $addend): string
    {
        $result = $amount + $addend;

        self::assertInteger($result);

        return (string) $result;
    }

    /** @psalm-pure */
    public static function subtract(string $amount, string $subtrahend): string
    {
        $result = $amount - $subtrahend;

        self::assertInteger($result);

        return (string) $result;
    }

    /** @psalm-pure */
    public static function multiply(string $amount, string $multiplier): string
    {
        $result = $amount * $multiplier;

        self::assertIntegerBounds($result);

        return (string) Number::fromNumber($result);
    }

    /** @psalm-pure */
    public static function divide(string $amount, string $divisor): string
    {
        if (self::compare($divisor, '0') === 0) {
            throw InvalidArgumentException::divisionByZero();
        }

        $result = $amount / $divisor;

        self::assertIntegerBounds($result);

        return (string) Number::fromNumber($result);
    }

    /** @psalm-pure */
    public static function ceil(string $number): string
    {
        return self::castInteger(ceil((float) $number));
    }

    /** @psalm-pure */
    public static function floor(string $number): string
    {
        return self::castInteger(floor((float) $number));
    }

    /**
     * @psalm-suppress MoreSpecificReturnType we know that trimming `-` produces a positive numeric-string here
     * @psalm-suppress LessSpecificReturnStatement we know that trimming `-` produces a positive numeric-string here
     * @psalm-pure
     */
    public static function absolute(string $number): string
    {
        return ltrim($number, '-');
    }

    /**
     * @psalm-param Money::ROUND_* $roundingMode
     *
     * @psalm-return numeric-string
     *
     * @psalm-pure
     */
    public static function round(string $number, int $roundingMode): string
    {
        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            $number = Number::fromNumber($number);

            if ($number->isHalf()) {
                return self::castInteger(ceil((float) $number->__toString()));
            }

            return self::castInteger(round((float) $number->__toString(), 0, Money::ROUND_HALF_UP));
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            $number = Number::fromNumber($number);

            if ($number->isHalf()) {
                return self::castInteger(floor((float) $number->__toString()));
            }

            return self::castInteger(round((float) $number->__toString(), 0, Money::ROUND_HALF_DOWN));
        }

        /** @psalm-suppress MixedArgument the type of $roundingMode is well known, but inference fails on vimeo/psalm:4.7.0@d4377c0baf3ffbf0b1ec6998e8d1be2a40971005 */
        return self::castInteger(round((float) $number, 0, $roundingMode));
    }

    /** @psalm-pure */
    public static function share(string $amount, string $ratio, string $total): string
    {
        return self::castInteger(floor($amount * $ratio / $total));
    }

    /** @psalm-pure */
    public static function mod(string $amount, string $divisor): string
    {
        if (self::compare($divisor, '0') === 0) {
            throw InvalidArgumentException::moduloByZero();
        }

        $result = $amount % $divisor;

        self::assertIntegerBounds($result);

        return (string) $result;
    }

    /**
     * Asserts that an integer value didn't become something else
     * (after some arithmetic operation).
     *
     * @throws OverflowException  If integer overflow occured.
     * @throws UnderflowException If integer underflow occured.
     *
     * @psalm-pure
     */
    private static function assertIntegerBounds(int|float $amount): void
    {
        if ($amount > PHP_INT_MAX) {
            throw new OverflowException('You overflowed the maximum allowed integer (PHP_INT_MAX)');
        }

        if ($amount < ~PHP_INT_MAX) {
            throw new UnderflowException('You underflowed the minimum allowed integer (PHP_INT_MAX)');
        }
    }

    /**
     * Casts an amount to integer ensuring that an overflow/underflow did not occur.
     *
     * @psalm-return numeric-string
     *
     * @psalm-pure
     */
    private static function castInteger(int|float $amount): string
    {
        self::assertIntegerBounds($amount);

        return (string) (int) $amount;
    }

    /**
     * Asserts that integer remains integer after arithmetic operations.
     *
     * @psalm-pure
     */
    private static function assertInteger(string|float|int $amount): void
    {
        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }
}
