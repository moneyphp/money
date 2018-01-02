<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use Money\Currency;
use PhpSpec\ObjectBehavior;

final class CurrencyListSpec extends ObjectBehavior
{
    use Matchers;

    function let()
    {
        $this->beConstructedWith([
            'MY1' => 2,
            'MY2' => 0,
            'MY3' => 1,
        ]);
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyList::class);
    }

    function it_contains_custom_currency()
    {
        $this->contains(new Currency('MY1'))->shouldReturn(true);
    }

    function it_does_not_contain_currency()
    {
        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    function it_is_iterable()
    {
        $this->getIterator()->shouldHaveCurrency('MY1');
    }
}
