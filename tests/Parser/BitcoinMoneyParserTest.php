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
            ["Ƀ1000.00", 100000],
            ["Ƀ1000.0",  100000],
            ["Ƀ1000.00", 100000],
            ["Ƀ0.01", 1],
            ["Ƀ0.00", 0],
            ["Ƀ1", 100],
            ["-Ƀ1000", -100000],
            ["-Ƀ1000.0", -100000],
            ["-Ƀ1000.00", -100000],
            ["-Ƀ0.01", -1],
            ["-Ƀ1", -100],
            ["Ƀ1000", 100000],
            ["Ƀ1000.0", 100000],
            ["Ƀ1000.00", 100000],
            ["Ƀ0.01", 1],
            ["Ƀ1", 100],
            ["Ƀ.99", 99],
            ["-Ƀ.99", -99],
            ["Ƀ.99", 99],
            ["Ƀ99.", 9900],
            ["Ƀ0", '0'],
        ];
    }
}
