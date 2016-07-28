<?php

namespace spec\Money\Formatter;

use Money\Currencies\CurrenciesWithSubunit;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, CurrenciesWithSubunit $currenciesWithSubunit)
    {
        $this->beConstructedWith($numberFormatter, $currenciesWithSubunit);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\IntlMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(\NumberFormatter $numberFormatter, CurrenciesWithSubunit $currenciesWithSubunit)
    {
        $money = new Money(1, new Currency('EUR'));

        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');
        $currenciesWithSubunit->getSubunitsFor($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('€1.00');
    }
}
