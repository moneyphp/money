<?php

namespace Tests\Money\Currencies;

use Money\Currency;
use Money\Currencies\ISOCurrencies;

final class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testItContainsCurrencies()
    {
        $currencies = new ISOCurrencies();

        $this->assertTrue($currencies->contains(new Currency('EUR')));
        $this->assertFalse($currencies->contains(new Currency('ASD')));
    }
}
