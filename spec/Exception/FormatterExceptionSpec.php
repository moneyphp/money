<?php

namespace spec\Money\Exception;

use Money\Exception;
use Money\Exception\FormatterException;
use PhpSpec\ObjectBehavior;

final class FormatterExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FormatterException::class);
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(Exception::class);
    }

    function it_is_a_runtime_exception()
    {
        $this->shouldHaveType(\RuntimeException::class);
    }
}
