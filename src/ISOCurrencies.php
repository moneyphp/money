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
        $this->currencies = $this->requireCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return array_key_exists($currency->getCode(), $this->currencies);
    }

    private function requireCurrencies()
    {
        $file = __DIR__.'/../vendor/umpirsky/currency-list/data/en/currency.php';
        if (file_exists($file)) {
            return require $file;
        }

        $file = __DIR__.'/../../../umpirsky/currency-list/data/en/currency.php';
        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
