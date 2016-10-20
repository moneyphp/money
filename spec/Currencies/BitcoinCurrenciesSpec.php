<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class BitcoinCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    function it_is_initializable()
    {
        $this->shouldHaveType(BitcoinCurrencies::class);
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

    function it_is_iterable()
    {
        $this->getIterator()->shouldHaveCurrency('XBT');
    }
}
