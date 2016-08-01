<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PhpSpec\ObjectBehavior;

class ISOCurrenciesSpec extends ObjectBehavior
{
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

    function it_throws_an_exception_when_currency_is_unknown()
    {
        $this->shouldThrow(UnknownCurrencyException::class)->duringFind('XXXX');
    }

    function it_provides_a_list_of_currencies()
    {
        $this->getIterator()->shouldReturnAnInstanceOf('ArrayIterator');
    }

    public function currencyCodeExamples()
    {
        $currencies = require __DIR__.'/../../vendor/moneyphp/iso-currencies/resources/current.php';
        $currencies = array_keys($currencies);

        return array_map(function($currency) { return [$currency]; }, $currencies);
    }
}
