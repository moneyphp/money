<?php

namespace Tests\Money;

use Money\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function test_it_converts_to_json()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }
}
