<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currency;
use Money\Exception\FormatterException;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Formatter\AggregateMoneyFormatter */
final class AggregateMoneyFormatterTest extends TestCase
{
    /** @test */
    public function it_formats_money(): void
    {
        $money        = new Money(1, new Currency('EUR'));
        $eurFormatter = $this->createMock(MoneyFormatter::class);

        $eurFormatter->method('format')
            ->with(self::equalTo($money))
            ->willReturn('FIRST');

        self::assertEquals(
            'FIRST',
            (new AggregateMoneyFormatter(['EUR' => $eurFormatter]))
                ->format($money)
        );
    }

    /** @test */
    public function it_throws_an_exception_when_no_formatter_for_currency_is_found(): void
    {
        $eurFormatter = $this->createMock(MoneyFormatter::class);

        $eurFormatter->expects(self::never())
            ->method('format');

        $formatter = new AggregateMoneyFormatter(['EUR' => $eurFormatter]);

        $this->expectException(FormatterException::class);
        $formatter->format(new Money(1, new Currency('USD')));
    }

    /** @test */
    public function it_uses_default_formatter_when_no_specific_one_is_found(): void
    {
        $eur              = new Money(1, new Currency('EUR'));
        $usd              = new Money(1, new Currency('USD'));
        $other            = new Money(1, new Currency('CZK'));
        $eurFormatter     = $this->createMock(MoneyFormatter::class);
        $usdFormatter     = $this->createMock(MoneyFormatter::class);
        $defaultFormatter = $this->createMock(MoneyFormatter::class);

        $eurFormatter->method('format')
            ->with(self::equalTo($eur))
            ->willReturn('EUR_FORMATTER');

        $usdFormatter->method('format')
            ->with(self::equalTo($usd))
            ->willReturn('USD_FORMATTER');

        $defaultFormatter->method('format')
            ->with(self::equalTo($other))
            ->willReturn('OTHER_FORMATTER');

        $formatter = new AggregateMoneyFormatter([
            'EUR' => $eurFormatter,
            'USD' => $usdFormatter,
            '*'   => $defaultFormatter,
        ]);

        self::assertEquals('EUR_FORMATTER', $formatter->format($eur));
        self::assertEquals('USD_FORMATTER', $formatter->format($usd));
        self::assertEquals('OTHER_FORMATTER', $formatter->format($other));
    }
}
