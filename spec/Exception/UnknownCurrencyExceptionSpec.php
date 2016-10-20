<?php

namespace spec\Money\Exception;

use Money\Exception;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

final class UnknownCurrencyExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UnknownCurrencyException::class);
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(Exception::class);
    }

    function it_is_a_domain_exception()
    {
        $this->shouldHaveType(\DomainException::class);
    }
}
