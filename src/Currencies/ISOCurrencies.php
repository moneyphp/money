<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;

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
            self::$currencies = $this->loadCurrencies();
        }

        return isset(self::$currencies[$currency->getCode()]);
    }

    private function loadCurrencies()
    {
        $file = __DIR__.'/../../vendor/moneyphp/iso-currencies/resources/current.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
