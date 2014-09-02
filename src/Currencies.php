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
 * General currencies implementation, can be used with custom ones
 *
 * @author Mathias Verraes
 */
class Currencies implements AvailableCurrencies
{
    /**
     * List of known currencies
     *
     * @var array[]
     */
    protected $currencies;

    /**
     * @param array $currencies
     */
    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return array_key_exists($currency->getCode(), $this->currencies);
    }
}
