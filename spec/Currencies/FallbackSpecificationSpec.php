<?php

namespace spec\Money\Currencies;

use Money\Currencies\Specification;
use Money\CurrenciesSpecification;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

class FallbackSpecificationSpec extends ObjectBehavior
{
    function let(CurrenciesSpecification $delegated, CurrenciesSpecification $fallback)
    {
        $this->beConstructedWith($delegated, $fallback);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\\Currencies\\FallbackSpecification');
    }

    function it_is_a_currencies_specification()
    {
        $this->shouldImplement(CurrenciesSpecification::class);
    }

    function it_has_a_specification(CurrenciesSpecification $delegated, CurrenciesSpecification $fallback)
    {
        $currency = new Currency('EUR');
        $delegated->specify($currency)->willThrow(UnknownCurrencyException::class);
        $fallback->specify($currency)->willReturn(new Specification('EUR', 2));

        $this->specify($currency)->shouldReturnAnInstanceOf('Money\\Currencies\\Specification');
    }
}
