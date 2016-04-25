<?php

namespace Tests\Money;

use Money\Currency;
use Money\CurrencyPair;

final class CurrencyPairTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonEncoding()
    {
        $expectedJson = '{"baseCurrency":"EUR","counterCurrency":"USD","ratio":1.25}';
        $actualJson = json_encode(new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.25));

        $this->assertEquals($expectedJson, $actualJson);
    }
}
