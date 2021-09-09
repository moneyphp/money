<?php

declare(strict_types=1);

namespace spec\Money\Exception;

use InvalidArgumentException;
use Money\Currency;
use Money\Exception;
use Money\Exception\UnresolvableCurrencyPairException;
use PhpSpec\ObjectBehavior;

final class UnresolvableCurrencyPairExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UnresolvableCurrencyPairException::class);
    }

    public function it_is_an_exception(): void
    {
        $this->shouldHaveType(Exception::class);
    }

    public function it_is_an_invalid_argument_exception(): void
    {
        $this->shouldHaveType(InvalidArgumentException::class);
    }

    public function it_accepts_a_currency_pair(): void
    {
        $this->createFromCurrencies(new Currency('EUR'), new Currency('USD'))
            ->shouldHaveType(UnresolvableCurrencyPairException::class);
    }
}
