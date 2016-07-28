<?php

namespace spec\Money\Currencies;

use Money\CurrenciesWithSubunit;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

class FallbackSubunitSpec extends ObjectBehavior
{
    function let(CurrenciesWithSubunit $delegated, CurrenciesWithSubunit $fallback)
    {
        $this->beConstructedWith($delegated, $fallback);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\FallbackSubunit');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(CurrenciesWithSubunit::class);
    }

    function it_has_a_subunit(CurrenciesWithSubunit $delegated, CurrenciesWithSubunit $fallback)
    {
        $currency = new Currency('EUR');
        $delegated->getSubunitFor($currency)->willThrow(UnknownCurrencyException::class);
        $fallback->getSubunitFor($currency)->willReturn(2);

        $this->getSubunitFor($currency)->shouldReturn(2);
    }
}
