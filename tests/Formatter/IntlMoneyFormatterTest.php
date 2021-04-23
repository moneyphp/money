<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Formatter\IntlMoneyFormatter */
final class IntlMoneyFormatterTest extends TestCase
{
    /**
     * @psalm-param non-empty-string $currency
     * @psalm-param positive-int $subunit
     * @psalm-param non-empty-string $result
     * @psalm-param positive-int $mode
     * @psalm-param positive-int|0 $fractionDigits
     *
     * @dataProvider moneyExamples
     * @test
     */
    public function itFormatsMoney(int $amount, string $currency, int $subunit, string $result, int $mode, bool $hasPattern, int $fractionDigits): void
    {
        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new NumberFormatter('en_US', $mode);

        if ($hasPattern) {
            $numberFormatter->setPattern('¤#,##0.00;-¤#,##0.00');
        }

        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $fractionDigits);

        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $currency === $givenCurrency->getCode()))
            ->willReturn($subunit);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        self::assertSame($result, $moneyFormatter->format($money));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int,
     *     non-empty-string,
     *     positive-int,
     *     non-empty-string,
     *     positive-int,
     *     bool,
     *     positive-int|0
     * }>
     */
    public static function moneyExamples(): array
    {
        return [
            [5005, 'USD', 2, '$50', NumberFormatter::CURRENCY, true, 0],
            [100, 'USD', 2, '$1.00', NumberFormatter::CURRENCY, true, 2],
            [41, 'USD', 2, '$0.41', NumberFormatter::CURRENCY, true, 2],
            [5, 'USD', 2, '$0.05', NumberFormatter::CURRENCY, true, 2],
            [5, 'USD', 2, '$0.050', NumberFormatter::CURRENCY, true, 3],
            [35, 'USD', 2, '$0.350', NumberFormatter::CURRENCY, true, 3],
            [135, 'USD', 2, '$1.350', NumberFormatter::CURRENCY, true, 3],
            [6135, 'USD', 2, '$61.350', NumberFormatter::CURRENCY, true, 3],
            [-6135, 'USD', 2, '-$61.350', NumberFormatter::CURRENCY, true, 3],
            [-6152, 'USD', 2, '-$61.5', NumberFormatter::CURRENCY, true, 1],
            [5, 'EUR', 2, '€0.05', NumberFormatter::CURRENCY, true, 2],
            [50, 'EUR', 2, '€0.50', NumberFormatter::CURRENCY, true, 2],
            [500, 'EUR', 2, '€5.00', NumberFormatter::CURRENCY, true, 2],
            [5, 'EUR', 2, '€0.05', NumberFormatter::DECIMAL, true, 2],
            [50, 'EUR', 2, '€0.50', NumberFormatter::DECIMAL, true, 2],
            [500, 'EUR', 2, '€5.00', NumberFormatter::DECIMAL, true, 2],
            [5, 'EUR', 2, '0', NumberFormatter::DECIMAL, false, 0],
            [50, 'EUR', 2, '0', NumberFormatter::DECIMAL, false, 0],
            [500, 'EUR', 2, '5', NumberFormatter::DECIMAL, false, 0],
            [5, 'EUR', 2, '5%', NumberFormatter::PERCENT, false, 0],
            [5055, 'USD', 2, '$51', NumberFormatter::CURRENCY, true, 0],
        ];
    }
}
