<?php

namespace Money\Bitcoin;

use Money\Currencies;
use Money\Currency;

/**
 * Bitcoin in Currencies list.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinCurrencies implements Currencies
{
    /**
     * @var Currencies
     */
    private $delegatedCurrencies;

    /**
     * @param Currencies $delegatedCurrencies
     */
    public function __construct(Currencies $delegatedCurrencies)
    {
        $this->delegatedCurrencies = $delegatedCurrencies;
    }

    /**
     * Checks whether a currency is available in the current context.
     *
     * @param Currency $currency
     *
     * @return bool
     */
    public function contains(Currency $currency)
    {
        if ($currency->getCode() === BitcoinFormatter::CODE) {
            return true;
        }

        return $this->delegatedCurrencies->contains($currency);
    }
}
