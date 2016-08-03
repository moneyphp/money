<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

class ISOCurrenciesSpec extends ObjectBehavior
{
    use HaveCurrencyTrait;

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Currencies\ISOCurrencies');
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }

    /**
     * @dataProvider currencyCodeExamples
     */
    function it_contains_iso_currencies($currency)
    {
        $this->contains(new Currency($currency))->shouldReturn(true);
    }

    /**
     * @dataProvider currencyCodeExamples
     */
    function it_has_a_subunit($currency)
    {
        $this->subunitFor(new Currency($currency))->shouldBeInteger();
    }

    function it_throws_an_exception_when_currency_is_unknown()
    {
        $this->shouldThrow(UnknownCurrencyException::class)->duringSubunitFor(new Currency('XXXX'));
    }

    function it_provides_an_iterator()
    {
        $this->getIterator()->shouldReturnAnInstanceOf(\Traversable::class);
        $this->getIterator()->shouldHaveCurrency('EUR');
    }

    public function currencyCodeExamples()
    {
        $currencies = require __DIR__.'/../../resources/currency.php';
        $currencies = array_keys($currencies);

        return array_map(function($currency) { return [$currency]; }, $currencies);
    }
}
