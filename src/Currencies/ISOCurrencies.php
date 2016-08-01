<?php

namespace Money\Currencies;

use ArrayIterator;
use IteratorAggregate;
use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Traversable;

/**
 * List of supported ISO 4217 currency codes and names.
 *
 * @author Mathias Verraes
 */
final class ISOCurrencies implements Currencies, IteratorAggregate
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
            ->withSubunit(self::$currencyData[$code]['minorUnit'])
            ->withName(self::$currencyData[$code]['currency']);

        return self::$currencies[$code];
    }

    /**
     * @return Traversable|Currency[]
     */
    public function getIterator()
    {
        $list = [];

        if (null === self::$currencyData) {
            self::$currencyData = $this->loadCurrencies();
        }

        $codes = array_keys(self::$currencyData);
        foreach ($codes as $code) {
            if (!isset(self::$currencies[$code])) {
                self::$currencies[$code] = (new Currency($code))
                    ->withSubunit(self::$currencyData[$code]['minorUnit'])
                    ->withName(self::$currencyData[$code]['currency']);
            }

            $list[] = self::$currencies[$code];
        }

        return new ArrayIterator($list);
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
