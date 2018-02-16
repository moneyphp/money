<?php

namespace Tests\Money;

use Money\Converter;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class ConverterTest extends TestCase
{
    /**
     * @dataProvider convertExamples
     * @test
     */
    public function it_converts_to_a_different_currency(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ) {
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        /** @var Exchange|ObjectProphecy $exchange */
        $exchange = $this->prophesize(Exchange::class);

        $converter = new Converter($currencies->reveal(), $exchange->reveal());

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->quote($baseCurrency, $counterCurrency)->willReturn($pair);

        $money = $converter->convert(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    /**
     * @dataProvider convertExamples
     * @test
     */
    public function it_converts_to_a_different_currency_when_decimal_separator_is_comma(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ) {
        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');

        $this->it_converts_to_a_different_currency(
            $baseCurrencyCode,
            $counterCurrencyCode,
            $subunitBase,
            $subunitCounter,
            $ratio,
            $amount,
            $expectedAmount
        );
    }

    public function convertExamples()
    {
        return [
            ['USD', 'JPY', 2, 0, 101, 100, 101],
            ['JPY', 'USD', 0, 2, 0.0099, 1000, 990],
            ['USD', 'EUR', 2, 2, 0.89, 100, 89],
            ['EUR', 'USD', 2, 2, 1.12, 100, 112],
            ['XBT', 'USD', 8, 2, 6597, 1, 0],
            ['XBT', 'USD', 8, 2, 6597, 10, 0],
            ['XBT', 'USD', 8, 2, 6597, 100, 1],
            ['ETH', 'EUR', 18, 2, 330.84, '100000000000000000', 3308],
            ['BTC', 'USD', 8, 2, 13500, 100000000, 1350000],
            ['USD', 'BTC', 2, 8, 1 / 13500, 1350000, 100000000],
        ];
    }
}
