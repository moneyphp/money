<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/** @covers \Money\MoneyFactory */
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

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals(new Money(20, $currency), $money);
    }

    /** @phpstan-return list<array{Currency}> */
    public static function currencyExamples(): array
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
