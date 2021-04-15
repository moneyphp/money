<?php

declare(strict_types=1);

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

final class DecimalMoneyFormatterSpec extends ObjectBehavior
{
    public function let(Currencies $currencies): void
    {
        $this->beConstructedWith($currencies);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DecimalMoneyFormatter::class);
    }

    public function it_is_a_money_formatter(): void
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    public function it_formats_money(Currencies $currencies): void
    {
        $money = new Money(100, new Currency('EUR'));

        $currencies->subunitFor($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('1.00');
    }
}
