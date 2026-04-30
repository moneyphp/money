<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Formatter\BitcoinMoneyFormatter */
final class BitcoinMoneyFormatterTest extends TestCase
{
    /**
     * @phpstan-param positive-int $value
     * @phpstan-param non-empty-string $formatted
     * @phpstan-param non-negative-int $fractionDigits
     *
     * @dataProvider bitcoinExamples
     * @test
     */
    public function itFormatsMoney(int $value, string $formatted, int $fractionDigits): void
    {
        $currencies = $this->createMock(Currencies::class);
        $currency   = new Currency('XBT');

        $currencies->method('subunitFor')
            ->with(self::equalTo($currency))
            ->willReturn(8);

        self::assertSame(
            $formatted,
            (new BitcoinMoneyFormatter($fractionDigits, $currencies))
                ->format(new Money($value, $currency))
        );
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     positive-int,
     *     non-empty-string,
     *     non-negative-int
     * }>
     */
    public static function bitcoinExamples(): array
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
