<?php

declare(strict_types=1);

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use NumberFormatter;
use PhpSpec\ObjectBehavior;

final class IntlMoneyFormatterSpec extends ObjectBehavior
{
    public function let(NumberFormatter $numberFormatter, Currencies $currencies): void
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(IntlMoneyFormatter::class);
    }

    public function it_is_a_money_formatter(): void
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    public function it_formats_money(NumberFormatter $numberFormatter, Currencies $currencies): void
    {
        $money = new Money(1, new Currency('EUR'));

        $numberFormatter->formatCurrency('0.01', 'EUR')->willReturn('€1.00');
        $currencies->subunitFor($money->getCurrency())->willReturn(2);

        $this->format($money)->shouldReturn('€1.00');
    }
}
