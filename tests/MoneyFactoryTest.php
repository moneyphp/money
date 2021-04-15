<?php

declare(strict_types=1);

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
    public function itCreatesMoneyUsingFactories(Currency $currency): void
    {
        $code  = $currency->getCode();
        $money = Money::{$code}(20);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency), $money);
    }

    /** @psalm-return list<array{Currency}> */
    public function currencyExamples(): array
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
