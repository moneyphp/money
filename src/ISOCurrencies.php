<?php

namespace Money;

use Alcohol\ISO4217;

/**
 * List of supported ISO 4217 currency codes and names.
 *
 * @author Mathias Verraes
 */
final class ISOCurrencies implements Currencies
{
    /**
     * List of known currencies.
     *
     * @var array
     */
    private static $currencies;

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        $iso4217 = new ISO4217();

        try {
            $iso4217->getByAlpha3($currency->getCode());
        } catch (\OutOfBoundsException $e) {
            return false;
        }

        return true;
    }
}
