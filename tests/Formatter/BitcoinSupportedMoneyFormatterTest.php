<?php

namespace Tests\Money\Formatter;

use Money\Formatter\BitcoinSupportedMoneyFormatter;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

final class BitcoinSupportedMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testRoundMoney()
    {
        $money = new Money(100000, new Currency('XBT'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 2);
        $this->assertEquals("\0xC9\0x831000.00", $formatter->format($money));
    }

    public function testLessThanOneHundred()
    {
        $money = new Money(41, new Currency('XBT'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 2);
        $this->assertEquals("\0xC9\0x830.41", $formatter->format($money));
    }

    public function testLessThanTen()
    {
        $money = new Money(5, new Currency('XBT'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 2);
        $this->assertEquals("\0xC9\0x830.05", $formatter->format($money));
    }

    public function testZeroFractionDigits()
    {
        $money = new Money(5, new Currency('XBT'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 0);
        $this->assertEquals("\0xC9\0x835", $formatter->format($money));
    }

    public function testDifferentFractionDigits()
    {
        $money = new Money(5, new Currency('XBT'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 4);
        $this->assertEquals("\0xC9\0x830.0005", $formatter->format($money));
    }

    public function testDifferentCurrency()
    {
        $money = new Money(5, new Currency('USD'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlFormatter = new IntlMoneyFormatter($numberFormatter);
        $formatter = new BitcoinSupportedMoneyFormatter($intlFormatter, 2);
        $this->assertEquals('$0.05', $formatter->format($money));
    }
}
