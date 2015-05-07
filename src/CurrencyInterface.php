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

use JsonSerializable;

/**
 * Currency Value Object interface
 *
 * Holds Currency specific data
 *
 * @author Mathias Verraes
 */
interface CurrencyInterface extends JsonSerializable
{
    /**
     * Returns the currency code
     *
     * @return string
     *
     * @deprecated Use getCode() instead
     */
    public function getName();

    /**
     * Returns the currency code
     *
     * @return string
     */
    public function getCode();

    /**
     * Checks whether this currency is the same as an other
     *
     * @param Currency $other
     *
     * @return boolean
     */
    public function equals(CurrencyInterface $other);

    /**
     * Checks whether this currency is available in the passed context
     *
     * @param AvailableCurrencies $currencies
     *
     * @return boolean
     */
    public function isAvailableWithin(AvailableCurrencies $currencies);

    /**
     * @return string
     */
    public function __toString();
}
