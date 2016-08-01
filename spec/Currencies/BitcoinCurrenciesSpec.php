<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BitcoinCurrenciesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\BitcoinCurrencies');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_contains_bitcoin()
    {
        $this->contains(new Currency('XBT'))->shouldReturn(true);
        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    function it_can_find_bitcoin()
    {
        $this->find('XBT')->shouldReturnAnInstanceOf('Money\\Currency');
    }

    function it_throws_an_exception_when_currency_is_unknown()
    {
        $this->shouldThrow(UnknownCurrencyException::class)->duringFind('XXXX');
    }
}
