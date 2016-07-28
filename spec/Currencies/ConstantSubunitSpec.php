<?php

namespace spec\Money\Currencies;

use Money\CurrenciesWithSubunit;
use Money\Currency;
use PhpSpec\ObjectBehavior;

class ConstantSubunitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\ConstantSubunit');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(CurrenciesWithSubunit::class);
    }

    function it_has_a_subunit()
    {
        $this->getSubunitFor(new Currency('EUR'))->shouldReturn(2);
    }
}
