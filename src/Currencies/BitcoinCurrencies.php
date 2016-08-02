<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

/**
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinCurrencies implements Currencies
{
    const CODE = 'XBT';
    const SYMBOL = "\0xC9\0x83";

    private static $currency;

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return self::CODE === $currency->getCode();
    }

    /**
     * @param string $code
     *
     * @return Currency
     *
     * @throws UnknownCurrencyException
     */
    public function find($code)
    {
        if ($code !== self::CODE) {
            throw new UnknownCurrencyException('Cannot find Bitcoin currency '.$code);
        }

        if (null === self::$currency) {
            self::$currency = (new Currency(self::CODE))
                ->withSubunit(8);
        }

        return self::$currency;
    }
}
