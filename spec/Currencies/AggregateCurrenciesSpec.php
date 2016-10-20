<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

final class AggregateCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    function let(Currencies $currencies, Currencies $otherCurrencies)
    {
        $this->beConstructedWith([
            $currencies,
            $otherCurrencies,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregateCurrencies::class);
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_throws_an_exception_when_invalid_currency_repository_is_passed()
    {
        $this->beConstructedWith(['currencies']);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_contains_currencies(Currencies $currencies, Currencies $otherCurrencies)
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(true);

        $this->contains($currency)->shouldReturn(true);
    }

    function it_might_not_contain_currencies(Currencies $currencies, Currencies $otherCurrencies)
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(false);

        $this->contains($currency)->shouldReturn(false);
    }

    function it_provides_subunit(Currencies $currencies, Currencies $otherCurrencies)
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(true);
        $otherCurrencies->subunitFor($currency)->willReturn(2);

        $this->subunitFor($currency)->shouldReturn(2);
    }

    function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown(Currencies $currencies, Currencies $otherCurrencies)
    {
        $currency = new Currency('XXXX');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(false);

        $this->shouldThrow(UnknownCurrencyException::class)->duringSubunitFor($currency);
    }

    function it_is_iterable(Currencies $currencies, Currencies $otherCurrencies)
    {
        $currencies->getIterator()->willReturn(new \ArrayIterator([new Currency('EUR')]));
        $otherCurrencies->getIterator()->willReturn(new \ArrayIterator([new Currency('USD')]));

        $this->getIterator()->shouldReturnAnInstanceOf(\Traversable::class);
        $this->getIterator()->shouldHaveCurrency('EUR');
        $this->getIterator()->shouldHaveCurrency('USD');
    }
}
