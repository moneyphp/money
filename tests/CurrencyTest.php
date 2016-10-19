<?php

namespace Tests\Money;

use Money\Currency;

final class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_converts_to_json()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }
}
