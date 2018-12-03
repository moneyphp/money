<?php

namespace spec\Money;

use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use PhpSpec\ObjectBehavior;

final class CurrencyPairSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Currency('EUR'), new Money(1.250000, new Currency('USD')));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyPair::class);
    }

    function it_is_json_serializable()
    {
        $this->shouldImplement(\JsonSerializable::class);
    }

    function it_has_currencies_and_ratio()
    {
        $this->beConstructedWith($baseCurrency = new Currency('EUR'), $counterRatio = new Money($ratioAmount = 1.0, $counterCurrency = new Currency('USD')));

        $this->getBaseCurrency()->shouldReturn($baseCurrency);
        $this->getCounterCurrency()->shouldReturn($counterCurrency);
        $this->getConversionRatio()->shouldReturn($counterRatio);
    }

    function it_throws_an_exception_when_ratio_is_not_numeric()
    {
        $this->beConstructedWith(new Currency('EUR'), new Currency('USD'), 'NON_NUMERIC');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_equals_to_another_currency_pair()
    {
        $this->equals(new CurrencyPair(new Currency('GBP'), new Money(1.250000, new Currency('USD'))))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Money(1.250000, new Currency('GBP'))))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Money(1.5000, new Currency('USD'))))->shouldReturn(false);
        $this->equals(new CurrencyPair(new Currency('EUR'), new Money(1.250000, new Currency('USD'))))->shouldReturn(true);
    }

    function it_parses_an_iso_string()
    {
        $pair = $this->createFromIso('EUR/USD 1.250000');

        $this->equals($pair)->shouldReturn(true);
    }

    function it_throws_an_exception_when_iso_string_cannot_be_parsed()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringCreateFromIso('1.250000');
    }
}
