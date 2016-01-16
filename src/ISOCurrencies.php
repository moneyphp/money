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
    private static $currencies;

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->requireCurrencies();
        }

        return array_key_exists($currency->getCode(), self::$currencies);
    }

    private function requireCurrencies()
    {
        $file = 'umpirsky/currency-list/data/en/currency.php';

        $path = __DIR__.'/../vendor/'.$file;
        if (file_exists($path)) {
            return require $path;
        }

        $path = __DIR__.'/../../../'.$file;
        if (file_exists($path)) {
            return require $path;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
