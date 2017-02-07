<?php

namespace spec\Money;

use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\HistoricalConverter;
use Money\HistoricalExchange;
use Money\Money;
use PhpSpec\ObjectBehavior;
use DateTime;

final class HistoricalConverterSpec extends ObjectBehavior
{
    function let(Currencies $currencies, HistoricalExchange $exchange)
    {
        $this->beConstructedWith($currencies, $exchange);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HistoricalConverter::class);
    }

    function it_converts_to_a_different_currency(Currencies $currencies, HistoricalExchange $exchange)
    {
        $baseCurrency = new Currency($baseCurrencyCode = 'ABC');
        $counterCurrency = new Currency($counterCurrencyCode = 'XYZ');
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, 0.5);
        $date = new DateTime();

        $currencies->subunitFor($baseCurrency)->willReturn(100);
        $currencies->subunitFor($counterCurrency)->willReturn(100);

        $exchange->historical($baseCurrency, $counterCurrency, $date)->willReturn($pair);

        $money = $this->convert(
            new Money(2, new Currency($baseCurrencyCode)),
            $counterCurrency,
            $date
        );

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBe('1');
        $money->getCurrency()->getCode()->shouldBe($counterCurrencyCode);
    }

    function it_converts_using_rounding_modes(Currencies $currencies, HistoricalExchange $exchange)
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, 1.25);
        $date = new DateTime();

        $currencies->subunitFor($baseCurrency)->willReturn(2);
        $currencies->subunitFor($counterCurrency)->willReturn(2);
        $exchange->historical($baseCurrency, $counterCurrency, $date)->willReturn($pair);

        $money = new Money(10, $baseCurrency);

        $resultMoney = $this->convert($money, $counterCurrency, $date);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(13);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');

        $resultMoney = $this->convert($money, $counterCurrency, $date, PHP_ROUND_HALF_DOWN);

        $resultMoney->shouldHaveType(Money::class);
        $resultMoney->getAmount()->shouldBeLike(12);
        $resultMoney->getCurrency()->getCode()->shouldReturn('USD');
    }
}
