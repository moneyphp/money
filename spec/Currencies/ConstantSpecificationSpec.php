<?php

namespace spec\Money\Currencies;

use Money\CurrenciesSpecification;
use Money\Currency;
use PhpSpec\ObjectBehavior;

class ConstantSpecificationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\\Currencies\\ConstantSpecification');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(CurrenciesSpecification::class);
    }

    function it_has_a_specification()
    {
        $this->specify(new Currency('EUR'))->shouldReturnAnInstanceOf('Money\\Currencies\\Specification');
    }
}
