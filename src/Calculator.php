<?php
namespace Money;

/**
 * Interface Calculator
 * @package Money
 */
interface Calculator
{

    /**
     * @return bool
     */
    public static function supported();

    /**
     * @param int|string $a
     * @param int|string $b
     * @return int
     */
    public function compare($a, $b);

    /**
     * @param int|string $amount
     * @param int|string $addend
     * @return mixed
     */
    public function add($amount, $addend);

    /**
     * @param int|string $amount
     * @param int|string $subtrahend
     * @return mixed
     */
    public function subtract($amount, $subtrahend);

    /**
     * @param int|string $amount
     * @param int|float $multiplier
     * @return mixed
     */
    public function multiply($amount, $multiplier);

    /**
     * @param int|string $amount
     * @param int|float $divisor
     * @return mixed
     */
    public function divide($amount, $divisor);

    /**
     * @param int|string|float $number
     * @return mixed
     */
    public function ceil($number);

    /**
     * @param int|string|float $number
     * @return mixed
     */
    public function floor($number);

    /**
     * @param int|string|float $number
     * @param int|string $roundingMode
     * @return mixed
     */
    public function round($number, $roundingMode);

    /**
     * @param int|string $amount
     * @param int|float $ratio
     * @param int|float $total
     * @return int|string
     */
    public function share($amount, $ratio, $total);

}
