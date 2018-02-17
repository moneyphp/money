<?php

namespace Tests\Money;

use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class MoneyFactoryTest extends TestCase
{
    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_using_factories(Currency $currency)
    {
        $code = $currency->getCode();
        $money = Money::{$code}(20);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency), $money);
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
