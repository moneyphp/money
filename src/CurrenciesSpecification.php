<?php

namespace Money;

use Money\Currencies\Specification;

/**
 * Specifies information about a currency.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
interface CurrenciesSpecification
{
    /**
     * @param Currency $currency
     *
     * @return Specification
     */
    public function specify(Currency $currency);
}
