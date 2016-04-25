<?php

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Exception\FormatterException;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

class AggregateMoneyFormatterSpec extends ObjectBehavior
{
    function it_is_initializable(MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith([
            'EUR' => $moneyFormatter,
        ]);

        $this->shouldHaveType('Money\Formatter\AggregateMoneyFormatter');
    }

    function it_is_a_money_formatter(MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith([
            'EUR' => $moneyFormatter,
        ]);

        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith([
            'EUR' => $moneyFormatter,
        ]);

        $money = new Money(1, new Currency('EUR'));

        $moneyFormatter->format($money)->willReturn('€1.00');

        $this->format($money)->shouldReturn('€1.00');
    }

    function it_throws_when_no_formatter_found(MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith([
            'EUR' => $moneyFormatter,
        ]);

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
