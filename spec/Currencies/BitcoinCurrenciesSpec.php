<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BitcoinCurrenciesSpec extends ObjectBehavior
{
    use HaveCurrencyTrait;

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

    function it_can_be_iterated()
    {
        $this->getIterator()->shouldReturnAnInstanceOf(\Traversable::class);
        $this->getIterator()->shouldHaveCurrency('XBT');
    }
}
