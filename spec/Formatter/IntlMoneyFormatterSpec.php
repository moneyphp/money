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


    function it_formats_with_subunits()
    {
        $money = new Money(500, new Currency('USD'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        $moneyFormatter = $this->withSubunits($numberFormatter, 2);
        $moneyFormatter->format($money)->shouldReturn('$5');

        $moneyFormatter = $this->withSubunits($numberFormatter, 0);
        $moneyFormatter->format($money)->shouldReturn('$500');
    }

    public function it_formats_without_fraction_subunits_pattern()
    {
        $money = new Money(500, new Currency('USD'));
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $this->beConstructedWith($numberFormatter);
        $this->format($money)->shouldReturn('$5.00');
    }
}
