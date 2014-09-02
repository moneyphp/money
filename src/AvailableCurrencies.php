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
 * Implement this to provide a list of currencies
 *
 * @author Mathias Verraes
 */
interface AvailableCurrencies
{
    /**
     * Checks whether a currency is available in the current context
     *
     * @param Currency $currency
     *
     * @return boolean
     */
    public function contains(Currency $currency);
}
