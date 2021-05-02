<?php

declare(strict_types=1);

namespace spec\Money\Exception;

use Money\Exception;
use Money\Exception\ParserException;
use PhpSpec\ObjectBehavior;
use RuntimeException;

final class ParserExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ParserException::class);
    }

    public function it_is_an_exception(): void
    {
        $this->shouldHaveType(Exception::class);
    }

    public function it_is_a_runtime_exception(): void
    {
        $this->shouldHaveType(RuntimeException::class);
    }
}
