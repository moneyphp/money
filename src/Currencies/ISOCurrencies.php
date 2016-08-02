<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * List of supported ISO 4217 currency codes and names.
 *
 * @author Mathias Verraes
 */
final class ISOCurrencies implements Currencies
{
    /**
     * Currency data from data source.
     *
     * @var array
     */
    private static $currencyData;
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
        if (null === self::$currencyData) {
            self::$currencyData = $this->loadCurrencies();
        }

        return isset(self::$currencyData[$currency->getCode()]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($code)
    {
        if (isset(self::$currencies[$code])) {
            return self::$currencies[$code];
        }

        if (null === self::$currencyData) {
            self::$currencyData = $this->loadCurrencies();
        }

        if (!isset(self::$currencyData[$code])) {
            throw new UnknownCurrencyException('Cannot find ISO currency '.$code);
        }

        self::$currencies[$code] = (new Currency($code))
            ->withSubunit(self::$currencyData[$code]['minorUnit']);

        return self::$currencies[$code];
    }

    /**
     * @return array
     */
    private function loadCurrencies()
    {
        $file = __DIR__.'/../../vendor/moneyphp/iso-currencies/resources/current.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
