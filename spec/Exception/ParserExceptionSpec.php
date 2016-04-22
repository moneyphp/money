<?php

namespace spec\Money\Exception;

use PhpSpec\ObjectBehavior;

class ParserExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Exception\ParserException');
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }
}
