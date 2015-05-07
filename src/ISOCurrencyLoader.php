<?php
namespace Money;

class ISOCurrencyLoader implements CurrencyLoader
{
    /**
     * List of known currencies
     *
     * @var array
     */
    private $currencies;

    public function __construct()
    {
        $this->currencies = require __DIR__.'/currencies.php';
    }

    public function load($code)
    {
        if (! isset($this->currencies[$code])) {
            throw new UnknownCurrencyException(sprintf("Failed to load currency '%s': not a valid ISO4217 code", $code));
        }

        return new Currency($code);
    }
}
