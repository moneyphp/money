<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function assert;

final class BitcoinMoneyFormatterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @psalm-param positive-int $value
     * @psalm-param non-empty-string $formatted
     * @psalm-param positive-int|0 $fractionDigits
     *
     * @dataProvider bitcoinExamples
     * @test
     */
    public function itFormatsMoney(int $value, string $formatted, int $fractionDigits): void
    {
        $currencies = $this->prophesize(Currencies::class);
        assert($currencies instanceof Currencies || $currencies instanceof ObjectProphecy);

        $formatter = new BitcoinMoneyFormatter($fractionDigits, $currencies->reveal());

        $currency = new Currency('XBT');
        $money    = new Money($value, $currency);

        $currencies->subunitFor($currency)->willReturn(8);

        $this->assertSame($formatted, $formatter->format($money));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     positive-int,
     *     non-empty-string,
     *     positive-int|0
     * }>
     */
    public function bitcoinExamples(): array
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
