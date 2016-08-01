<?php

namespace Money;

use Money\Exception\UnknownCurrencyException;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
interface Currencies
{
    /**
     * Checks whether a currency is available in the current context.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function contains(Currency $currency);

    /**
     * @param string $code
     *
     * @return Currency
     *
     * @throws UnknownCurrencyException
     */
    public function find($code);
}
