<?php

namespace spec\Money\Exchange;

use Exchanger\CurrencyPair as ExchangerCurrencyPair;
use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception;
use Exchanger\Exchanger;
use Exchanger\ExchangeRateQuery;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\ExchangerExchange;
use Money\Exchange\SwapExchange;
use Swap\Swap;
use PhpSpec\ObjectBehavior;

final class ExchangerExchangeSpec extends ObjectBehavior
{
    function let(Exchanger $exchanger)
    {
        $this->beConstructedWith($exchanger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExchangerExchange::class);
    }

    function it_is_an_exchange()
    {
        $this->shouldImplement(Exchange::class);
    }

    function it_exchanges_currencies(Exchanger $exchanger, ExchangeRate $exchangeRate)
    {
        $exchangeRate->getValue()->willReturn('1.0');

        $query = new ExchangeRateQuery(new ExchangerCurrencyPair('EUR', 'USD'));
        $exchanger->getExchangeRate($query)->willReturn($exchangeRate);

        $currencyPair = $this->quote($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    function it_throws_an_exception_when_cannot_exchange_currencies(Exchanger $exchanger)
    {
        $query = new ExchangeRateQuery(new ExchangerCurrencyPair('EUR', 'XYZ'));
        $exchanger->getExchangeRate($query)->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('EUR'), new Currency('XYZ'));
    }
}
