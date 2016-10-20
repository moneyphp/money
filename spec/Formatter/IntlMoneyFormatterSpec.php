<?php

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

final class IntlMoneyFormatterSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IntlMoneyFormatter::class);
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $money = new Money(1, new Currency('EUR'));

        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');
        $currencies->subunitFor($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('€1.00');
    }
}
