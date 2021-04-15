<?php

declare(strict_types=1);

namespace spec\Money\Formatter;

use Money\Currency;
use Money\Exception\FormatterException;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

final class AggregateMoneyFormatterSpec extends ObjectBehavior
{
    public function let(MoneyFormatter $moneyFormatter): void
    {
        $this->beConstructedWith(['EUR' => $moneyFormatter]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AggregateMoneyFormatter::class);
    }

    public function it_is_a_money_formatter(): void
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    public function it_formats_money(MoneyFormatter $moneyFormatter): void
    {
        $money = new Money(1, new Currency('EUR'));

        $moneyFormatter->format($money)->willReturn('€1.00');

        $this->format($money)->shouldReturn('€1.00');
    }

    public function it_throws_an_exception_when_no_formatter_for_currency_is_found(): void
    {
        $money = new Money(1, new Currency('USD'));

        $this->shouldThrow(FormatterException::class)->duringFormat($money);
    }

    public function it_uses_default_formatter_when_no_specific_one_is_found(MoneyFormatter $moneyFormatter, MoneyFormatter $moneyFormatter2): void
    {
        $this->beConstructedWith([
            'USD' => $moneyFormatter,
            '*' => $moneyFormatter2,
        ]);

        $money = new Money(1, new Currency('EUR'));

        $moneyFormatter2->format($money)->willReturn('€1.00');

        $this->format($money)->shouldReturn('€1.00');
    }
}
