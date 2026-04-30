<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\BitcoinMoneyParser;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Parser\BitcoinMoneyParser */
final class BitcoinMoneyParserTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $string
     * @phpstan-param int|numeric-string $units
     *
     * @dataProvider bitcoinExamples
     * @test
     */
    public function itParsesMoney(string $string, int|string $units): void
    {
        $moneyParser = new BitcoinMoneyParser(2);

        $money = $moneyParser->parse($string);

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals($units, $money->getAmount());
        self::assertEquals(BitcoinCurrencies::CODE, $money->getCurrency()->getCode());
    }

    /**
     * @test
     */
    public function forceCurrencyWorks(): void
    {
        $moneyParser = new BitcoinMoneyParser(2);

        $money = $moneyParser->parse("\xC9\x830.25", new Currency('ETH'));

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals('25', $money->getAmount());
        self::assertEquals('ETH', $money->getCurrency()->getCode());
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     non-empty-string,
     *     int|numeric-string
     * }>
     */
    public static function bitcoinExamples(): array
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
            ["\xC9\x830000.020000000", 2],
            ["-\xC9\x830000.020000000", -2],
            ["-\xC9\x83000000", 0],
        ];
    }
}
