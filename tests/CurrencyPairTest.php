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

    /** @test */
    public function it_parses_an_iso_string(): void
    {
        $this->assertEquals(
            new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1.250000'),
            CurrencyPair::createFromIso('EUR/USD 1.250000')
        );
    }

    /** @test */
    public function it_equals_to_another_currency_pair(): void
    {
        $pair = new CurrencyPair(
            new Currency('EUR'),
            new Currency('USD'),
            '1.250000'
        );

        $this->assertFalse($pair->equals(new CurrencyPair(
            new Currency('GBP'),
            new Currency('USD'),
            '1.250000'
        )));

        $this->assertFalse($pair->equals(new CurrencyPair(
            new Currency('EUR'),
            new Currency('GBP'),
            '1.250000'
        )));

        $this->assertFalse($pair->equals(new CurrencyPair(
            new Currency('EUR'),
            new Currency('USD'),
            '1.5000'
        )));

        $this->assertTrue($pair->equals(new CurrencyPair(
            new Currency('EUR'),
            new Currency('USD'),
            '1.250000'
        )));
    }
}
