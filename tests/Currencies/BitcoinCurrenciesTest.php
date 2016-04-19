<?php

namespace Tests\Money\Currencies;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;

final class BitcoinCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $bitcoinCurrencies = new BitcoinCurrencies();

        $this->assertTrue($bitcoinCurrencies->contains(new Currency('XBT')));
    }
}
