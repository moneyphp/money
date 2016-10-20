<?php

namespace spec\Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use PhpSpec\ObjectBehavior;

final class FixedExchangeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'EUR' => [
                'USD' => 1.25
            ]
        ]);
    }

    function it_exchanges_currencies()
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');

        $currencyPair = $this->quote($baseCurrency, $counterCurrency);

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($baseCurrency);
        $currencyPair->getCounterCurrency()->shouldReturn($counterCurrency);
        $currencyPair->getConversionRatio()->shouldReturn(1.25);
    }

    function it_cannot_exchange_currencies()
    {
        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('USD'), new Currency('EUR'));
    }
}
