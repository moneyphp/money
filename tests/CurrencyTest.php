<?php

namespace Tests\Money;

use Money\Currency;

final class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonEncoding()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }
}
