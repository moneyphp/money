<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AggregateCurrenciesSpec extends ObjectBehavior
{
    function let(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $this->beConstructedWith([
            $isoCurrencies,
            $otherCurrencies,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\AggregateCurrencies');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_contains_currencies(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(true);

        $this->contains(new Currency('EUR'))->shouldReturn(true);
    }

    function testItDoesNotContainCurrencies(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(true);

        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    function it_throws_an_exception_when_currency_is_unknown(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->find('XXXX')->willThrow(UnknownCurrencyException::class);
        $otherCurrencies->find('XXXX')->willThrow(UnknownCurrencyException::class);

        $this->shouldThrow(UnknownCurrencyException::class)->duringFind('XXXX');
    }

    function testConstructorThrowsAnException()
    {
        $this->beConstructedWith(['currencies']);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
