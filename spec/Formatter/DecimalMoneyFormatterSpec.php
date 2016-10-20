<?php

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

final class DecimalMoneyFormatterSpec extends ObjectBehavior
{
    function let(Currencies $currencies)
    {
        $this->beConstructedWith($currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DecimalMoneyFormatter::class);
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(Currencies $currencies)
    {
        $money = new Money(100, new Currency('EUR'));

        $currencies->subunitFor($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('1.00');
    }
}
