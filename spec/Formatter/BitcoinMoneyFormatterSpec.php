<?php

namespace spec\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Exception\FormatterException;
use Money\Money;
use Money\MoneyFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BitcoinMoneyFormatterSpec extends ObjectBehavior
{
    function let(Currencies $bitcoinCurrencies)
    {
        $this->beConstructedWith(2, $bitcoinCurrencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Formatter\BitcoinMoneyFormatter');
    }

    function it_is_a_money_formatter()
    {
        $this->shouldImplement(MoneyFormatter::class);
    }

    /**
     * @dataProvider bitcoinExamples
     */
    function it_formats_money($value, $formatted, $fractionDigits, Currencies $bitcoinCurrencies)
    {
        $this->beConstructedWith($fractionDigits, $bitcoinCurrencies);

        $currency = new Currency('XBT');
        $money = new Money($value, $currency);

        $bitcoinCurrencies->subunitFor($currency)->willReturn(8);
        $this->format($money)->shouldReturn($formatted);
    }

    public function bitcoinExamples()
    {
        return [
            [100000000000, "\0xC9\0x831000.00", 2],
            [1000000000000, "\0xC9\0x8310000.00", 2],
            [41000000, "\0xC9\0x830.41", 2],
            [5000000, "\0xC9\0x830.05", 2],
            [500000000, "\0xC9\0x835", 0],
            [50000, "\0xC9\0x830.0005", 4],
            [100000500000, "\0xC9\0x831000.01", 2],
            [100099500000, "\0xC9\0x831001.00", 2],
            [999999600000, "\0xC9\0x8310000.00", 2],
        ];
    }

    function it_does_not_format_a_different_currency()
    {
        $money = new Money(5, new Currency('USD'));

        $this->shouldThrow(FormatterException::class)->duringFormat($money);
    }
}
