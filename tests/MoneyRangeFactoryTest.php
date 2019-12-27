<?php

namespace Tests\Money;

use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\MoneyRange;
use PHPUnit\Framework\TestCase;

final class MoneyRangeFactoryTest extends TestCase
{
    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_range_using_factories(Currency $currency)
    {
        $code = $currency->getCode();
        $range = MoneyRange::{$code}(10, 20);

        $this->assertInstanceOf(MoneyRange::class, $range);
        $this->assertEquals(
            new MoneyRange(new Money(10, $currency), new Money(20, $currency)),
            $range
        );
    }

    public function currencyExamples()
    {
        $currencies = new AggregateCurrencies([
            new ISOCurrencies(),
            new BitcoinCurrencies(),
        ]);

        $examples = [];

        foreach ($currencies as $currency) {
            $examples[] = [$currency];
        }

        return $examples;
    }
}
