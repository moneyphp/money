<?php

namespace Tests\Money\Bitcoin;

use Money\Bitcoin\BitcoinCurrencies;
use Money\Currency;
use Money\ISOCurrencies;

final class BitcoinCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $currencies = new ISOCurrencies();
        $bitcoinCurrencies = new BitcoinCurrencies($currencies);

        $this->assertTrue($bitcoinCurrencies->contains(new Currency('XBT')));
    }
}
