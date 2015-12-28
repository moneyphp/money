<?php

namespace Money;

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
    private $currencies;

    public function __construct()
    {
        $this->currencies = require __DIR__.'/../data/currencies.php';
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return array_key_exists($currency->getCode(), $this->currencies);
    }
}
