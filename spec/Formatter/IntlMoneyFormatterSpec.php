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
        $numberFormatter->formatCurrency('1.', 'EUR')->willReturn('€0.01');

        $money = new Money(1, new Currency('EUR'));

        $this->format($money)->shouldReturn('€0.01');
    }


    function it_formats_with_subunits(\NumberFormatter $numberFormatter)
    {
        $this->beConstructedWith($numberFormatter, 2);

        $numberFormatter->formatCurrency('5.00', 'USD')->willReturn('$5');

        $money = new Money(500, new Currency('USD'));

        $this->format($money)->shouldReturn('$5');
    }
}
