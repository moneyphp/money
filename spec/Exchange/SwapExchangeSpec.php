<?php

namespace spec\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\SwapExchange;
use Money\HistoricalExchange;
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

    function it_is_an_exchange()
    {
        $this->shouldImplement(Exchange::class);
        $this->shouldImplement(HistoricalExchange::class);
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

    function it_exchanges_currencies_for_a_historical_date(Swap $swap, ExchangeRate $exchangeRate)
    {
        $exchangeRate->getValue()->willReturn(1.0);

        $date = new \DateTime();
        $swap->historical('EUR/USD', $date)->willReturn($exchangeRate);

        $currencyPair = $this->historical($base = new Currency('EUR'), $counter = new Currency('USD'), $date);

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    function it_throws_an_exception_when_cannot_exchange_historical_currencies(Swap $swap)
    {
        $date = new \DateTime();

        $swap->historical('EUR/XYZ', $date)->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringHistorical(new Currency('EUR'), new Currency('XYZ'), $date);
    }
}
