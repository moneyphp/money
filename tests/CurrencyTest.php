<?php

namespace Tests\Money;

use function json_encode;
use Money\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function itConvertsToJson()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }
}
