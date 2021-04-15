<?php

declare(strict_types=1);

namespace spec\Money\Currencies;

use ArrayIterator;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;
use Traversable;

final class AggregateCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    public function let(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $this->beConstructedWith([
            $currencies,
            $otherCurrencies,
        ]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AggregateCurrencies::class);
    }

    public function it_is_a_currency_repository(): void
    {
        $this->shouldImplement(Currencies::class);
    }

    public function it_contains_currencies(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(true);

        $this->contains($currency)->shouldReturn(true);
    }

    public function it_might_not_contain_currencies(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(false);

        $this->contains($currency)->shouldReturn(false);
    }

    public function it_provides_subunit(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $currency = new Currency('EUR');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(true);
        $otherCurrencies->subunitFor($currency)->willReturn(2);

        $this->subunitFor($currency)->shouldReturn(2);
    }

    public function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $currency = new Currency('XXXX');

        $currencies->contains($currency)->willReturn(false);
        $otherCurrencies->contains($currency)->willReturn(false);

        $this->shouldThrow(UnknownCurrencyException::class)->duringSubunitFor($currency);
    }

    public function it_is_iterable(Currencies $currencies, Currencies $otherCurrencies): void
    {
        $currencies->getIterator()->willReturn(new ArrayIterator([new Currency('EUR')]));
        $otherCurrencies->getIterator()->willReturn(new ArrayIterator([new Currency('USD')]));

        $this->getIterator()->shouldReturnAnInstanceOf(Traversable::class);
        $this->getIterator()->shouldHaveCurrency('EUR');
        $this->getIterator()->shouldHaveCurrency('USD');
    }
}
