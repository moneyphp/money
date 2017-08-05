<?php

namespace spec\Money\Currencies;

use Money\Currencies\ArrayCurrencies;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class ArrayCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    function let()
    {
        $this->beConstructedWith([
            'MY1' => ['minorUnit' => 2, 'numericCode' => 1],
            'MY2' => ['minorUnit' => 0, 'numericCode' => 2],
            'MY3' => ['minorUnit' => 1, 'numericCode' => 3],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayCurrencies::class);
    }

    function it_contains_bitcoin()
    {
        $this->contains(new Currency('MY1'))->shouldReturn(true);
        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    function it_is_iterable()
    {
        $this->getIterator()->shouldHaveCurrency('MY1');
    }
}