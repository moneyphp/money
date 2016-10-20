<?php

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Exception\FormatterException;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;

final class BitcoinMoneyFormatterSpec extends ObjectBehavior
{
    function let(Currencies $bitcoinCurrencies)
    {
        $this->beConstructedWith(2, $bitcoinCurrencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BitcoinMoneyFormatter::class);
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    function it_formats_money(Currencies $bitcoinCurrencies)
    {
        $this->beConstructedWith(1, $bitcoinCurrencies);

        $currency = new Currency('XBT');
        $money = new Money(1000000, $currency);

        $bitcoinCurrencies->subunitFor($currency)->willReturn(8);

        $formatted = $this->format($money);

        $formatted->shouldBeString();
        $formatted->shouldContain(Currencies\BitcoinCurrencies::SYMBOL);
    }

    function it_throws_an_exception_when_currency_is_not_bitcoin()
    {
        $money = new Money(5, new Currency('USD'));

        $this->shouldThrow(FormatterException::class)->duringFormat($money);
    }
}
