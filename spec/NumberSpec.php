<?php

namespace spec\Money;

use Money\Number;
use PhpSpec\ObjectBehavior;

final class NumberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('1');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Number::class);
    }

    function it_throws_an_exception_when_number_is_invalid()
    {
        $this->beConstructedWith('ONE');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_creates_a_number_from_float()
    {
        $number = $this->fromFloat(1.1);

        $number->shouldHaveType(Number::class);
        $number->__toString()->shouldReturn('1.1');
    }

    function it_throws_an_exception_when_number_is_not_float_during_creation_from_float()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromFloat(1);
    }

    function it_throws_an_exception_when_number_is_not_numeric_during_creation_from_number()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromNumber(false);
    }
}
