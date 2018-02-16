<?php

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class BitcoinMoneyFormatterTest extends TestCase
{
    /**
     * @dataProvider bitcoinExamples
     * @test
     */
    public function it_formats_money($value, $formatted, $fractionDigits)
    {
        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        $formatter = new BitcoinMoneyFormatter($fractionDigits, $currencies->reveal());

        $currency = new Currency('XBT');
        $money = new Money($value, $currency);

        $currencies->subunitFor($currency)->willReturn(8);

        $this->assertSame($formatted, $formatter->format($money));
    }

    public function bitcoinExamples()
    {
        return [
            [100000000000, "\xC9\x831000.00", 2],
            [1000000000000, "\xC9\x8310000.00", 2],
            [41000000, "\xC9\x830.41", 2],
            [5000000, "\xC9\x830.05", 2],
            [500000000, "\xC9\x835", 0],
            [50000, "\xC9\x830.0005", 4],
            [100000500000, "\xC9\x831000.01", 2],
            [100099500000, "\xC9\x831001.00", 2],
            [999999600000, "\xC9\x8310000.00", 2],
            [100, "\xC9\x830.00", 2],
        ];
    }
}
