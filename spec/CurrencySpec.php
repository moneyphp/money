<?php

namespace spec\Money;

use Money\Currency;
use PhpSpec\ObjectBehavior;

final class CurrencySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Currency::class);
    }

    function it_is_json_serializable()
    {
        $this->shouldImplement(\JsonSerializable::class);
    }

    function it_throws_an_exception_when_code_is_not_string()
    {
        $this->beConstructedWith(123);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
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
