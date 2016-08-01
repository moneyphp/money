<?php

namespace spec\Money;

use Money\Currency;
use PhpSpec\ObjectBehavior;

class CurrencySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currency');
    }

    function it_throws_an_exception_when_code_is_not_string()
    {
        $this->beConstructedWith(123);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_subunit_is_not_integer()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringWithSubunit('test');
    }

    function it_throws_an_exception_when_name_is_not_string()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringWithName(1);
    }

    function it_has_a_code()
    {
        $this->getCode()->shouldReturn('EUR');
    }

    function it_equals_to_a_currency_with_the_same_code()
    {
        $this->equals(new Currency('EUR'))->shouldReturn(true);
        $this->equals(new Currency('USD'))->shouldReturn(false);
    }
}
