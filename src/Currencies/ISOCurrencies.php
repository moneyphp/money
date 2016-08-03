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

    /**
     * {@inheritdoc}
     */
    public function subunitFor(Currency $currency)
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        if (!isset(self::$currencies[$currency->getCode()])) {
            throw new UnknownCurrencyException('Cannot find ISO currency '.$currency->getCode());
        }

        return self::$currencies[$currency->getCode()]['minorUnit'];
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        if (null === self::$currencies) {
            self::$currencies = $this->loadCurrencies();
        }

        return new \ArrayIterator(
            array_map(
                function ($code) {
                    return new Currency($code);
                },
                array_keys(self::$currencies)
            )
        );
    }

    /**
     * @return array
     */
    private function loadCurrencies()
    {
        $file = __DIR__.'/../../resources/currency.php';

        if (file_exists($file)) {
            return require $file;
        }

        throw new \RuntimeException('Failed to load currency ISO codes.');
    }
}
