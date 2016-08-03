<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AggregateCurrenciesSpec extends ObjectBehavior
{
    use HaveCurrencyTrait;

    function let(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $this->beConstructedWith([
            $isoCurrencies,
            $otherCurrencies,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\AggregateCurrencies');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    function it_contains_currencies(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(true);

        $this->contains(new Currency('EUR'))->shouldReturn(true);
    }

    function it_can_be_iterated(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->getIterator()->willReturn(new \ArrayIterator([new Currency('EUR')]));
        $otherCurrencies->getIterator()->willReturn(new \ArrayIterator([new Currency('USD')]));

        $this->getIterator()->shouldReturnAnInstanceOf(\Traversable::class);
        $this->getIterator()->shouldHaveCurrency('EUR');
        $this->getIterator()->shouldHaveCurrency('USD');
    }

    function it_provides_subunit(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(true);
        $otherCurrencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $this->subunitFor(new Currency('EUR'))->shouldReturn(2);
    }

    function it_throws_an_exception_when_currency_is_unknown(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(false);

        $this->shouldThrow(UnknownCurrencyException::class)->duringSubunitFor(new Currency('XXXX'));
    }

    function testItDoesNotContainCurrencies(Currencies $isoCurrencies, Currencies $otherCurrencies)
    {
        $isoCurrencies->contains(Argument::type(Currency::class))->willReturn(false);
        $otherCurrencies->contains(Argument::type(Currency::class))->willReturn(true);

        $this->contains(new Currency('EUR'))->shouldReturn(false);
    }

    function testConstructorThrowsAnException()
    {
        $this->beConstructedWith(['currencies']);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
