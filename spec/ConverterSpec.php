<?php

namespace spec\Money;

use Money\Calculator;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConverterSpec extends ObjectBehavior
{
    function let(Currencies $currencies, Exchange $exchange)
    {
        $this->beConstructedWith($currencies, $exchange);
    }

    /**
     * @dataProvider convertExamples
     */
    function it_converts_to_a_different_currency(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount,
        Currencies $currencies,
        Exchange $exchange
    ) {
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->quote($baseCurrency, $counterCurrency)->willReturn($pair);

        $money = $this->convert(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($expectedAmount);
        $money->getCurrency()->shouldBeLike($counterCurrencyCode);
    }

    function it_converts_using_rounding_modes(Currencies $currencies, Exchange $exchange)
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, 1.25);

        $currencies->subunitFor($baseCurrency)->willReturn(2);
        $currencies->subunitFor($counterCurrency)->willReturn(2);
        $exchange->quote($baseCurrency, $counterCurrency)->willReturn($pair);

        $money = new Money(10, $baseCurrency);

        $resultMoney = $this->convert($money, $counterCurrency);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(13);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');

        $resultMoney = $this->convert($money, $counterCurrency, PHP_ROUND_HALF_DOWN);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(12);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');
    }

    public function convertExamples()
    {
        return [
            ['USD', 'JPY', 2, 0, 101, 100, 101],
            ['JPY', 'USD', 0, 2, 0.0099, 1000, 990],
            ['USD', 'EUR', 2, 2, 0.89, 100, 89],
            ['EUR', 'USD', 2, 2, 1.12, 100, 112],
        ];
    }
}
