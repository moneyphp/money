<?php

declare(strict_types=1);

namespace Money;

/**
 * Money calculations abstracted away from the Money value object.
 *
 * @internal the calculator component is an internal detail of this library: it is only supposed to be replaced if
 *           your system requires a custom architecture for operating on large numbers.
 */
interface Calculator
{
    /**
     * Returns whether the calculator is supported in
     * the current server environment.
     */
    public static function supported(): bool;

    /**
     * Compare a to b.
     */
    public function compare(string $a, string $b): int;

    /**
     * Add added to amount.
     */
    public function add(string $amount, string $addend): string;

    /**
     * Subtract subtrahend from amount.
     */
    public function subtract(string $amount, string $subtrahend): string;

    /**
     * Multiply amount with multiplier.
     */
    public function multiply(string $amount, int|float|string $multiplier): string;

    /**
     * Divide amount with divisor.
     */
    public function divide(string $amount, int|float|string $divisor): string;

    /**
     * Round number to following integer.
     */
    public function ceil(string $number): string;

    /**
     * Round number to preceding integer.
     */
    public function floor(string $number): string;

    /**
     * Returns the absolute value of the number.
     */
    public function absolute(string $number): string;

    /**
     * Round number, use rounding mode for tie-breaker.
     */
    public function round(int|float|string $number, int $roundingMode): string;

    /**
     * Share amount among ratio / total portions.
     */
    public function share(string $amount, int|float|string $ratio, int|float|string $total): string;

    /**
     * Get the modulus of an amount.
     */
    public function mod(string $amount, int|float|string $divisor): string;
}
