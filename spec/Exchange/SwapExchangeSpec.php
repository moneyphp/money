<?php

namespace spec\Money\Exchange;

use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\Contract\ExchangeRateQuery;
use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\SwapExchange;
use Prophecy\Argument;
use Swap\Swap;
use PhpSpec\ObjectBehavior;

final class SwapExchangeSpec extends ObjectBehavior
{
    function let(Swap $swap)
    {
        $this->beConstructedWith($swap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SwapExchange::class);
    }

    function it_exchanges_currencies(Swap $swap, ExchangeRate $exchangeRate)
    {
        $exchangeRate->getValue()->willReturn(1.0);

        $swap->latest('EUR/USD')->willReturn($exchangeRate);

        $currencyPair = $this->quote($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    function it_throws_an_exception_when_cannot_exchange_currencies(Swap $swap)
    {
        $swap->latest('EUR/XYZ')->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('EUR'), new Currency('XYZ'));
    }

    function it_exchanges_currencies_with_an_exchange_rate_provider(ExchangeRateProvider $exchangeRateProvider, ExchangeRate $exchangeRate)
    {
        $this->beConstructedWith($exchangeRateProvider);

        $exchangeRateProvider->getExchangeRate(Argument::type(ExchangeRateQuery::class))->willReturn($exchangeRate);
        $exchangeRate->getValue()->willReturn(1.0);

        $currencyPair = $this->quote($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    function it_throws_an_exception_when_cannot_exchange_currencies_with_an_exchange_rate_provider(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->beConstructedWith($exchangeRateProvider);

        $exchangeRateProvider->getExchangeRate(Argument::type(ExchangeRateQuery::class))->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('EUR'), new Currency('XYZ'));
    }

    function it_throws_an_exception_when_exchange_is_not_swap_and_not_exchange_rate_provider()
    {
        $this->beConstructedWith(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
