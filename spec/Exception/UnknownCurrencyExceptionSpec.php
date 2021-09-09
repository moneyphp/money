<?php

declare(strict_types=1);

namespace spec\Money\Exception;

use DomainException;
use Money\Exception;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

final class UnknownCurrencyExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UnknownCurrencyException::class);
    }

    public function it_is_an_exception(): void
    {
        $this->shouldHaveType(Exception::class);
    }

    public function it_is_a_domain_exception(): void
    {
        $this->shouldHaveType(DomainException::class);
    }
}
