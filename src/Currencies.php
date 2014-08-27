<?php

/**
 * This file is part of the Money library.
 *
 * Copyright (c) 2011-2014 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

/**
 * Currency Repository
 *
 * Holds all currency data
 *
 * @author Mathias Verraes
 */
class Currencies
{
    /**
     * List of known currencies
     *
     * @var array[]
     */
    private $currencies;

    /**
     * @param array $currencies
     */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * Checks if a currency exists
     *
     * @param string $currency
     *
     * @return boolean
     */
    public function exists($currency)
    {
        return array_key_exists($currency, $this->currencies);
    }

    /**
     * Asserts that a currency exists in the current repository
     *
     * @param string $currency
     *
     * @throws UnknownCurrencyException If currency does not exists
     */
    public function assertExists($currency)
    {
        if (!$this->exists($currency)) {
            throw new UnknownCurrencyException($currency);
        }
    }

    /**
     * Creates a new Currency instance
     *
     * @param string $currency
     *
     * @return Currency
     */
    public function createCurrency($currency)
    {
        return new Currency($currency, $this);
    }
}
