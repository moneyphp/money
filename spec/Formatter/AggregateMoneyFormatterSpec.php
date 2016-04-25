<?php

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Exception\FormatterException;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class AggregateMoneyFormatterSpec extends ObjectBehavior
{
    function let (MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith([
            'EUR' => $moneyFormatter,
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\AggregateMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(MoneyFormatter $moneyFormatter)
    {
        $money = new Money(1, new Currency('EUR'));

        $moneyFormatter->format($money)->willReturn('€1.00');

        $this->format($money)->shouldReturn('€1.00');
    }

    function it_throws_when_no_formatter_found()
    {
        $money = new Money(1, new Currency('USD'));

        $this->shouldThrow(FormatterException::class)->duringFormat($money);
    }

    function it_uses_default_when_no_specific_found(MoneyFormatter $moneyFormatter1, MoneyFormatter $moneyFormatter2)
    {
        $this->beConstructedWith([
            'USD' => $moneyFormatter1,
            '*' => $moneyFormatter2,
        ]);

        $money = new Money(1, new Currency('EUR'));

        $moneyFormatter2->format($money)->willReturn('€1.00');

        $this->format($money)->shouldReturn('€1.00');
    }
}
