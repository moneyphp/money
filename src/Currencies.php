<?php

namespace Money;

use Money\Exception\UnknownCurrencyException;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
interface Currencies extends \IteratorAggregate
{
    /**
     * Checks whether a currency is available in the current context.
     *
     * @return bool
     */
    public function contains(Currency $currency);

    /**
     * Returns the subunit for a currency.
     *
     * @return int
     *
     * @throws UnknownCurrencyException If currency is not available in the current context
     */
    public function subunitFor(Currency $currency);
}
