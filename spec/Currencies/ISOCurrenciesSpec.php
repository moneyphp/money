<?php

namespace spec\Money\Currencies;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use PhpSpec\ObjectBehavior;

final class ISOCurrenciesSpec extends ObjectBehavior
{
    use Matchers;

    function it_is_initializable()
    {
        $this->shouldHaveType(ISOCurrencies::class);
    }

    function it_is_a_currency_repository()
    {
        $this->shouldImplement(Currencies::class);
    }
}
