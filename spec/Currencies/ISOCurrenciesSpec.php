<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currency;
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

    public function currencyCodeExamples()
    {
        $currencies = require __DIR__.'/../../resources/currency.php';
        $currencies = array_keys($currencies);

        return array_map(function($currency) { return [$currency]; }, $currencies);
    }
}
