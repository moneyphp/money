<?php

namespace Money;

/**
 * Provides the subunit for a currency.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
interface CurrenciesWithSubunit
{
    /**
     * @param Currency $currency
     *
     * @return int
     */
    public function getSubunitFor(Currency $currency);
}
