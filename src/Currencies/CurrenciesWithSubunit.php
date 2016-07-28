<?php

namespace Money\Currencies;

use Money\Currency;

interface CurrenciesWithSubunit
{
    /**
     * @param Currency $currency
     *
     * @return int
     */
    public function getSubunitsFor(Currency $currency);
}
