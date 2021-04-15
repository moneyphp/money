<?php

declare(strict_types=1);

namespace spec\Money\Calculator;

use Money\Calculator\PhpCalculator;
use OverflowException;
use PhpSpec\ObjectBehavior;
use UnderflowException;
use UnexpectedValueException;

use const PHP_INT_MAX;

final class PhpCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PhpCalculator::class);
    }

    public function it_throws_an_exception_when_overflown(): void
    {
        $this->shouldThrow(OverflowException::class)->duringMultiply(PHP_INT_MAX, 2);
    }

    public function it_throws_an_exception_when_underflown(): void
    {
        $this->shouldThrow(UnderflowException::class)->duringMultiply(~PHP_INT_MAX, 2);
    }

    public function throws_an_exception_when_the_result_is_not_integer(): void
    {
        $this->shouldThrow(UnexpectedValueException::class)->duringAdd(PHP_INT_MAX, 1);
    }
}
