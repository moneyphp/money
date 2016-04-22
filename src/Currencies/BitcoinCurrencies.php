<?php

namespace Money\Currencies;

use Money\Currencies;
use Money\Currency;

/**
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BitcoinCurrencies implements Currencies
{
    const CODE = 'XBT';
    const SYMBOL = "\0xC9\0x83";

    /**
     * {@inheritdoc}
     */
    public function contains(Currency $currency)
    {
        return self::CODE === $currency->getCode();
    }
}
