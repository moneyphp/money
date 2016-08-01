<?php

namespace Tests\Money\Currencies;

use Money\Currencies\ISOCurrencies;

final class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testIterator()
    {
        $currencies = new ISOCurrencies();
        foreach ($currencies as $currency) {
            $this->assertTrue(is_string($currency->getCode()));
            $this->assertTrue(is_int($currency->getSubunit()));
            $this->assertTrue(is_string($currency->getName()));
        }
    }

    public function testFind()
    {
        $currencies = new ISOCurrencies();
        $euro = $currencies->find('EUR');
        $this->assertSame($euro, $currencies->find('EUR'));
    }
}
