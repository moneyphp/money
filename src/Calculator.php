<?php

namespace Money;

/**
 * Calculator Interface
 *
 * @author Frederik Bosch
 */
interface Calculator
{

    /**
     * Returns whether the calculator is supported in
     * the current server environment
     *
     * @return bool
     */
    public static function supported();

    /**
     * Compare a to b
     *
     * @param int|string $a
     * @param int|string $b
     * @return int
     */
    public function compare($a, $b);

    /**
     * Add added to amount
     *
     * @param int|string $amount
     * @param int|string $addend
     * @return int|string
     */
    public function add($amount, $addend);

    /**
     * Subtract subtrahend from amount
     * @param int|string $amount
     * @param int|string $subtrahend
     * @return int|string
     */
    public function subtract($amount, $subtrahend);

    /**
     * Multiply amount with multiplier
     *
     * @param int|string $amount
     * @param int|float $multiplier
     * @return int|string
     */
    public function multiply($amount, $multiplier);

    /**
     * Divide amount with divisor
     *
     * @param int|string $amount
     * @param int|float $divisor
     * @return int|string
     */
    public function divide($amount, $divisor);

    /**
     * Round number to following integer
     *
     * @param int|string|float $number
     * @return int|string
     */
    public function ceil($number);

    /**
     * Round number to preceding integer
     *
     * @param int|string|float $number
     * @return int|string
     */
    public function floor($number);

    /**
     * Round number, use rounding mode for tie-breaker
     *
     * @param int|string|float $number
     * @param int|string $roundingMode
     * @return int|string
     */
    public function round($number, $roundingMode);

    /**
     * Share amount among ratio / total portions
     *
     * @param int|string $amount
     * @param int|float $ratio
     * @param int|float $total
     * @return int|string
     */
    public function share($amount, $ratio, $total);
}
