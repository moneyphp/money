<?php

namespace Tests\Money;

use Money\Currency;
use Money\ISOCurrencies;

final class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $currencies = new ISOCurrencies();

        $this->assertTrue($currencies->contains(new Currency('EUR')));
        $this->assertFalse($currencies->contains(new Currency('ASD')));
    }
}
