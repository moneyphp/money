<?php

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Money;
use Prophecy\Prophecy\ObjectProphecy;

final class BitcoinMoneyFormatterTest extends \PHPUnit_Framework_TestCase
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
            [100000000000, "Ƀ1000.00", 2],
            [1000000000000, "Ƀ10000.00", 2],
            [41000000, "Ƀ0.41", 2],
            [5000000, "Ƀ0.05", 2],
            [500000000, "Ƀ5", 0],
            [50000, "Ƀ0.0005", 4],
            [100000500000, "Ƀ1000.01", 2],
            [100099500000, "Ƀ1001.00", 2],
            [999999600000, "Ƀ10000.00", 2],
            [100, "Ƀ0.00", 2],
        ];
    }
}
