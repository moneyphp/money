<?php

declare(strict_types=1);

namespace spec\Money;

use InvalidArgumentException;
use Money\Number;
use PhpSpec\Exception\Example\PendingException;
use PhpSpec\ObjectBehavior;

final class NumberSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('1');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Number::class);
    }

    public function it_throws_an_exception_when_number_is_invalid(): void
    {
        $this->beConstructedWith('ONE');

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_creates_a_number_from_float(): void
    {
        $number = $this->fromFloat(1.1);

        $number->shouldHaveType(Number::class);
        $number->__toString()->shouldReturn('1.1');
    }

    public function it_throws_an_exception_when_number_is_not_float_during_creation_from_float(): void
    {
        throw new PendingException('Test not valid according to type definition - can be scrapped');

        $this->shouldThrow(InvalidArgumentException::class)->duringFromFloat(1);
    }

    public function it_throws_an_exception_when_number_is_not_numeric_during_creation_from_number(): void
    {
        throw new PendingException('Test not valid according to type definition - can be scrapped');

        $this->shouldThrow(InvalidArgumentException::class)->duringFromNumber(false);
    }
}
