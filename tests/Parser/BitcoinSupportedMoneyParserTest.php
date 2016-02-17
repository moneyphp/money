<?php

namespace Tests\Money\Parser;

use Money\Parser\BitcoinSupportedMoneyParser;
use Money\Parser\IntlMoneyParser;

final class BitcoinSupportedMoneyParserTest extends \PHPUnit_Framework_TestCase
{
    public static function provideFormattedMoney()
    {
        return [
            ["\0xC9\0x831000.00", 100000],
            ["\0xC9\0x831000.0",  100000],
            ["\0xC9\0x831000.00", 100000],
            ["\0xC9\0x830.01", 1],
            ["\0xC9\0x831", 100],
            ["-\0xC9\0x831000", -100000],
            ["-\0xC9\0x831000.0", -100000],
            ["-\0xC9\0x831000.00", -100000],
            ["-\0xC9\0x830.01", -1],
            ["-\0xC9\0x831", -100],
            ["\0xC9\0x831000", 100000],
            ["\0xC9\0x831000.0", 100000],
            ["\0xC9\0x831000.00", 100000],
            ["\0xC9\0x830.01", 1],
            ["\0xC9\0x831", 100],
            ["\0xC9\0x83.99", 99],
            ["-\0xC9\0x83.99", -99],
            ["\0xC9\0x830", '0'],
        ];
    }

    /**
     * @dataProvider provideFormattedMoney
     */
    public function testParse($string, $units)
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlParser = new IntlMoneyParser($formatter);

        $parser = new BitcoinSupportedMoneyParser($intlParser, 2);
        $this->assertEquals($units, $parser->parse($string, 'USD')->getAmount());
    }

    public function testParseDifferentCurrency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlParser = new IntlMoneyParser($formatter);

        $parser = new BitcoinSupportedMoneyParser($intlParser, 2);
        $this->assertEquals('100000', $parser->parse('$1000.00', 'USD')->getAmount());
    }

    public function testForceCurrency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $intlParser = new IntlMoneyParser($formatter);
        $parser = new BitcoinSupportedMoneyParser($intlParser, 2);
        $parsed = $parser->parse('$1000.00', 'XBT');

        $this->assertEquals('100000', $parsed->getAmount());
        $this->assertEquals('XBT', $parsed->getCurrency()->getCode());
    }
}
