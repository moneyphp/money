<?php

namespace spec\Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\ReversedCurrenciesExchange;
use PhpSpec\ObjectBehavior;

final class ReversedCurrenciesExchangeSpec extends ObjectBehavior
{
    function let(Exchange $exchange)
    {
        $this->beConstructedWith($exchange);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReversedCurrenciesExchange::class);
    }

    function it_is_an_exchange()
    {
        $this->shouldImplement(Exchange::class);
    }

    function it_exchanges_currencies(Exchange $exchange)
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');
        $currencyPair = new CurrencyPair($baseCurrency, $counterCurrency, 1.25);

        $exchange->quote($baseCurrency, $counterCurrency)->willReturn($currencyPair);

        $this->quote($baseCurrency, $counterCurrency)->shouldreturn($currencyPair);
    }

    function it_exchanges_reversed_currencies_when_the_original_pair_is_not_found(Exchange $exchange)
    {
        $baseCurrency = new Currency('USD');
        $counterCurrency = new Currency('EUR');
        $conversionRatio = 1.25;
        $currencyPair = new CurrencyPair($counterCurrency, $baseCurrency, $conversionRatio);

        $exchange->quote($baseCurrency, $counterCurrency)->willThrow(UnresolvableCurrencyPairException::class);
        $exchange->quote($counterCurrency, $baseCurrency)->willReturn($currencyPair);

        $currencyPair = $this->quote($baseCurrency, $counterCurrency);

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($baseCurrency);
        $currencyPair->getCounterCurrency()->shouldReturn($counterCurrency);
        $currencyPair->getConversionRatio()->shouldReturn(1 / $conversionRatio);
    }

    function it_throws_an_exception_when_neither_the_original_nor_the_reversed_currency_pair_can_be_resolved(Exchange $exchange)
    {
        $baseCurrency = new Currency('USD');
        $counterCurrency = new Currency('EUR');

        // Exceptions are not matched based on identity, but instance and properties
        $exchange->quote($baseCurrency, $counterCurrency)->willThrow(UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency));
        $exchange->quote($counterCurrency, $baseCurrency)->willThrow(UnresolvableCurrencyPairException::createFromCurrencies($counterCurrency, $baseCurrency));

        $this->shouldThrow(UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency))->duringQuote($baseCurrency, $counterCurrency);
    }
}
