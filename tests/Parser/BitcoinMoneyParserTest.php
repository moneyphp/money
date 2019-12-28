<?php

namespace Tests\Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\BitcoinMoneyParser;
use PHPUnit\Framework\TestCase;

final class BitcoinMoneyParserTest extends TestCase
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

    /**
     * @test
     */
    public function force_currency_works()
    {
        $moneyParser = new BitcoinMoneyParser(2);

        $money = $moneyParser->parse("\xC9\x830.25", new Currency('ETH'));

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('25', $money->getAmount());
        $this->assertEquals('ETH', $money->getCurrency()->getCode());
    }

    public function bitcoinExamples()
    {
        return [
            ["\xC9\x831000.00", 100000],
            ["\xC9\x831000.0",  100000],
            ["\xC9\x831000.00", 100000],
            ["\xC9\x830.01", 1],
            ["\xC9\x830.00", 0],
            ["\xC9\x831", 100],
            ["-\xC9\x831000", -100000],
            ["-\xC9\x831000.0", -100000],
            ["-\xC9\x831000.00", -100000],
            ["-\xC9\x830.01", -1],
            ["-\xC9\x831", -100],
            ["\xC9\x831000", 100000],
            ["\xC9\x831000.0", 100000],
            ["\xC9\x831000.00", 100000],
            ["\xC9\x830.01", 1],
            ["\xC9\x831", 100],
            ["\xC9\x83.99", 99],
            ["-\xC9\x83.99", -99],
            ["\xC9\x83.99", 99],
            ["\xC9\x8399.", 9900],
            ["\xC9\x830", '0'],
            ["\xC9\x830.020000000", 2],
        ];
    }
}
