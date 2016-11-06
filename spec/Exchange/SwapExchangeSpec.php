<?php

namespace spec\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\SwapExchange;
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
}
