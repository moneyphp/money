<?php

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter)
    {
        $this->beConstructedWith($numberFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\IntlMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(\NumberFormatter $numberFormatter)
    {
        $numberFormatter->getAttribute(\NumberFormatter::FRACTION_DIGITS)->willReturn(2);
        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');

        $money = new Money(1, new Currency('EUR'));

        $this->format($money)->shouldReturn('€1.00');
    }
}
