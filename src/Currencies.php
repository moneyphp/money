<?php

namespace Money;

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
}
