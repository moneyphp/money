<?php

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use Money\SubUnitProvider;
use PhpSpec\ObjectBehavior;

class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, SubUnitProvider $subUnitProvider)
    {
        $this->beConstructedWith($numberFormatter, $subUnitProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\IntlMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(\NumberFormatter $numberFormatter, SubUnitProvider $subUnitProvider)
    {
        $money = new Money(1, new Currency('EUR'));

        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');
        $subUnitProvider->provide($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('€1.00');
    }
}
