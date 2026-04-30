<?php

declare(strict_types=1);

namespace Tests\Money\Formatter;

use Money\Currencies;
use Money\Currency;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Money;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Formatter\IntlLocalizedDecimalFormatter */
final class IntlLocalizedDecimalFormatterTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $currency
     * @phpstan-param positive-int $subunit
     * @phpstan-param non-empty-string $result
     * @phpstan-param non-negative-int $fractionDigits
     *
     * @dataProvider moneyExamples
     * @test
     */
    public function itFormatsMoney(int $amount, string $currency, int $subunit, string $result, int $mode, $fractionDigits): void
    {
        $money = new Money($amount, new Currency($currency));

        $numberFormatter = new NumberFormatter('en_US', $mode);

        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $fractionDigits);

        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $currency === $givenCurrency->getCode()))
            ->willReturn($subunit);

        $moneyFormatter = new IntlLocalizedDecimalFormatter($numberFormatter, $currencies);
        self::assertSame($result, $moneyFormatter->format($money));
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     int,
     *     non-empty-string,
     *     positive-int,
     *     non-empty-string,
     *     int,
     *     non-negative-int
     * }>
     */
    public static function moneyExamples(): array
    {
        return [
            [5005, 'USD', 2, '50', NumberFormatter::DECIMAL, 0],
            [100, 'USD', 2, '1.00', NumberFormatter::DECIMAL, 2],
            [41, 'USD', 2, '0.41', NumberFormatter::DECIMAL, 2],
            [5, 'USD', 2, '0.05', NumberFormatter::DECIMAL, 2],
            [5, 'USD', 2, '0.050', NumberFormatter::DECIMAL, 3],
            [35, 'USD', 2, '0.350', NumberFormatter::DECIMAL, 3],
            [135, 'USD', 2, '1.350', NumberFormatter::DECIMAL, 3],
            [6135, 'USD', 2, '61.350', NumberFormatter::DECIMAL, 3],
            [-6135, 'USD', 2, '-61.350', NumberFormatter::DECIMAL, 3],
            [-6152, 'USD', 2, '-61.5', NumberFormatter::DECIMAL, 1],
            [5, 'EUR', 2, '0.05', NumberFormatter::DECIMAL, 2],
            [50, 'EUR', 2, '0.50', NumberFormatter::DECIMAL, 2],
            [500, 'EUR', 2, '5.00', NumberFormatter::DECIMAL, 2],
            [5, 'EUR', 2, '0.05', NumberFormatter::DECIMAL, 2],
            [50, 'EUR', 2, '0.50', NumberFormatter::DECIMAL, 2],
            [500, 'EUR', 2, '5.00', NumberFormatter::DECIMAL, 2],
            [5, 'EUR', 2, '0', NumberFormatter::DECIMAL, 0],
            [50, 'EUR', 2, '0', NumberFormatter::DECIMAL, 0],
            [500, 'EUR', 2, '5', NumberFormatter::DECIMAL, 0],
            [5055, 'USD', 2, '51', NumberFormatter::DECIMAL, 0],
        ];
    }
}
