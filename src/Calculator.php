<?php

declare(strict_types=1);

namespace Money;

use Money\Exception\InvalidArgumentException;

/**
 * Money calculations abstracted away from the Money value object.
 *
 * @internal the calculator component is an internal detail of this library: it is only supposed to be replaced if
 *           your system requires a custom architecture for operating on large numbers.
 */
interface Calculator
{
    /**
     * Compare a to b.
     *
     * Retrieves a negative value if $a < $b.
     * Retrieves a positive value if $a > $b.
     * Retrieves zero if $a == $b
     *
     * @phpstan-param numeric-string $a
     * @phpstan-param numeric-string $b
     *
     * @phpstan-pure
     */
    public static function compare(string $a, string $b): int;

    /**
     * Add added to amount.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $addend
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function add(string $amount, string $addend): string;

    /**
     * Subtract subtrahend from amount.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $subtrahend
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function subtract(string $amount, string $subtrahend): string;

    /**
     * Multiply amount with multiplier.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $multiplier
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function multiply(string $amount, string $multiplier): string;

    /**
     * Divide amount with divisor.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $divisor
     *
     * @phpstan-return numeric-string
     *
     * @throws InvalidArgumentException when $divisor is zero.
     *
     * @phpstan-pure
     */
    public static function divide(string $amount, string $divisor): string;

    /**
     * Round number to following integer.
     *
     * @phpstan-param numeric-string $number
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function ceil(string $number): string;

    /**
     * Round number to preceding integer.
     *
     * @phpstan-param numeric-string $number
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function floor(string $number): string;

    /**
     * Returns the absolute value of the number.
     *
     * @phpstan-param numeric-string $number
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function absolute(string $number): string;

    /**
     * Round number, use rounding mode for tie-breaker.
     *
     * @phpstan-param numeric-string $number
     * @phpstan-param Money::ROUND_* $roundingMode
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function round(string $number, int $roundingMode): string;

    /**
     * Share amount among ratio / total portions.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $ratio
     * @phpstan-param numeric-string $total
     *
     * @phpstan-return numeric-string
     *
     * @phpstan-pure
     */
    public static function share(string $amount, string $ratio, string $total): string;

    /**
     * Get the modulus of an amount.
     *
     * @phpstan-param numeric-string $amount
     * @phpstan-param numeric-string $divisor
     *
     * @phpstan-return numeric-string
     *
     * @throws InvalidArgumentException when $divisor is zero.
     *
     * @phpstan-pure
     */
    public static function mod(string $amount, string $divisor): string;
}
