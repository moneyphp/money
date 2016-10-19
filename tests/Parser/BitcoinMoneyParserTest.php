<?php

namespace Tests\Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Money;
use Money\Parser\BitcoinMoneyParser;

final class BitcoinMoneyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider bitcoinExamples
     * @test
     */
    public function it_parses_money($string, $units)
    {
        $moneyParser = new BitcoinMoneyParser(2);

        $money = $moneyParser->parse($string);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($units, $money->getAmount());
        $this->assertEquals(BitcoinCurrencies::CODE, $money->getCurrency()->getCode());
    }

    public function bitcoinExamples()
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
}
