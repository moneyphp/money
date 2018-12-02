<?php

namespace spec\Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\FixedExchange;
use Money\Money;
use PhpSpec\ObjectBehavior;

final class FixedExchangeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'EUR' => [
                'USD' => 1.25,
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FixedExchange::class);
    }

    function it_is_an_exchange()
    {
        $this->shouldImplement(Exchange::class);
    }

    function it_exchanges_currencies()
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');

        $currencyPair = $this->quote($baseCurrency, $counterCurrency);

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($baseCurrency);
        $currencyPair->getConversionRatio()->shouldReturn(new Money($counterCurrency, 1.25));
    }

    function it_cannot_exchange_currencies()
    {
        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('USD'), new Currency('EUR'));
    }
}
