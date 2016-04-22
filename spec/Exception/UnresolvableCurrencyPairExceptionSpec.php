<?php

namespace spec\Money\Exception;

use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use PhpSpec\ObjectBehavior;

class UnresolvableCurrencyPairExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Exception\UnresolvableCurrencyPairException');
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_accepts_a_currency_pair()
    {
        $this->createFromCurrencies(new Currency('EUR'), new Currency('USD'))
            ->shouldHaveType(UnresolvableCurrencyPairException::class);
    }
}
