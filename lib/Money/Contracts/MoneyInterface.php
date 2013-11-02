<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Contracts;

interface MoneyInterface
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * Create a Money instance
     * @param  integer                   $amount    Amount, expressed in the smallest units of $currency (eg cents)
     * @param  \Money\Contracts\Currency $currency
     * @throws \Money\InvalidArgumentException
     */
    public function __construct($amount, CurrencyInterface $currency);

    /**
     * @param \Money\Contracts\MoneyInterface $other
     * @return bool
     */
    public function isSameCurrency(MoneyInterface $other);

    /**
     * @param \Money\Contracts\MoneyInterface $other
     * @return bool
     */
    public function equals(MoneyInterface $other);

    /**
     * @param \Money\Contracts\MoneyInterface $other
     * @return int
     */
    public function compare(MoneyInterface $other);

    /**
     * @param \Money\Contracts\MoneyInterface $other
     * @return bool
     */
    public function greaterThan(MoneyInterface $other);

    /**
     * @param \Money\Contracts\MoneyInterface $other
     * @return bool
     */
    public function lessThan(MoneyInterface $other);

    /**
     * @deprecated Use getAmount() instead
     * @return int
     */
    public function getUnits();

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return \Money\Contracts\CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param \Money\Contracts\MoneyInterface $addend
     * @return \Money\Contracts\MoneyInterface 
     */
    public function add(MoneyInterface $addend);

    /**
     * @param \Money\Contracts\MoneyInterface $subtrahend
     * @return \Money\Contracts\MoneyInterface
     */
    public function subtract(MoneyInterface $subtrahend);

    /**
     * @param $multiplier
     * @param int $rounding_mode
     * @return \Money\Contracts\MoneyInterface
     */
    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP);

    /**
     * @param $divisor
     * @param int $rounding_mode
     * @return \Money\Contracts\MoneyInterface
     */
    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP);

    /**
     * Allocate the money according to a list of ratio's
     * @param array $ratios List of ratio's
     * @return \Money\Contracts\MoneyInterface
     */
    public function allocate(array $ratios = array());

    /** @return bool */
    public function isZero();

    /** @return bool */
    public function isPositive();

    /** @return bool */
    public function isNegative();

    /**
     * @param $string
     * @throws \Money\InvalidArgumentException
     * @return int
     */
    public static function stringToUnits( $string );
}