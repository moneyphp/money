<?php

namespace spec\Money\Formatter;

use Money\Currencies\Specification;
use Money\CurrenciesSpecification;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, CurrenciesSpecification $currencies)
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\IntlMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(\NumberFormatter $numberFormatter, CurrenciesSpecification $currencies)
    {
        $money = new Money(1, new Currency('EUR'));

        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');
        $currencies->specify($money->getCurrency())->willReturn(new Specification('EUR', 2));

        $this->format($money)->shouldReturn('€1.00');
    }
}
