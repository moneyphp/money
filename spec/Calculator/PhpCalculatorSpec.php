<?php

namespace spec\Money\Calculator;

use Money\Calculator\PhpCalculator;
use PhpSpec\ObjectBehavior;

final class PhpCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType(PhpCalculator::class);
    }

    function it_throws_an_exception_when_overflown()
    {
        $this->shouldThrow(\OverflowException::class)->duringMultiply(PHP_INT_MAX, 2);
    }

    function it_throws_an_exception_when_underflown()
    {
        $this->shouldThrow(\UnderflowException::class)->duringMultiply(~PHP_INT_MAX, 2);
    }

    function throws_an_exception_when_the_result_is_not_integer()
    {
        $this->shouldThrow(\UnexpectedValueException::class)->duringAdd(PHP_INT_MAX, 1);
    }
}
