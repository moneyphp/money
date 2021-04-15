<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Currency;
use Money\CurrencyPair;
use PHPUnit\Framework\TestCase;

use function json_encode;

final class CurrencyPairTest extends TestCase
{
    /**
     * @test
     */
    public function itConvertsToJson(): void
    {
        $expectedJson = '{"baseCurrency":"EUR","counterCurrency":"USD","ratio":"1.25"}';
        $actualJson   = json_encode(new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1.25'));

        $this->assertEquals($expectedJson, $actualJson);
    }
}
