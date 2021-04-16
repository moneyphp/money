<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Converter;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use PHPUnit\Framework\TestCase;

use const LC_ALL;

final class ConverterTest extends TestCase
{
    /**
     * @psalm-param non-empty-string $baseCurrencyCode
     * @psalm-param non-empty-string $counterCurrencyCode
     * @psalm-param positive-int|0 $subunitBase
     * @psalm-param positive-int|0 $subunitCounter
     * @psalm-param int|float $ratio
     * @psalm-param positive-int|numeric-string $amount
     * @psalm-param positive-int|0 $expectedAmount
     *
     * @dataProvider convertExamples
     * @test
     */
    public function itConvertsToADifferentCurrency(
        string $baseCurrencyCode,
        string $counterCurrencyCode,
        int $subunitBase,
        int $subunitCounter,
        int|float $ratio,
        int|string $amount,
        int $expectedAmount
    ): void {
        $baseCurrency    = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair            = new CurrencyPair($baseCurrency, $counterCurrency, (string) $ratio);

        $currencies = $this->createMock(Currencies::class);
        $exchange   = $this->createMock(Exchange::class);
        $converter  = new Converter($currencies, $exchange);

        $currencies->method('subunitFor')
            ->with(self::logicalOr(self::equalTo($baseCurrency), self::equalTo($counterCurrency)))
            ->willReturnCallback(
                static fn (Currency $currency): int => $currency->equals($baseCurrency) ? $subunitBase : $subunitCounter
            );

        $exchange->method('quote')
            ->with(self::equalTo($baseCurrency), self::equalTo($counterCurrency))
            ->willReturn($pair);

        $money = $converter->convert(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    /**
     * @psalm-param non-empty-string $baseCurrencyCode
     * @psalm-param non-empty-string $counterCurrencyCode
     * @psalm-param positive-int|0 $subunitBase
     * @psalm-param positive-int|0 $subunitCounter
     * @psalm-param int|float $ratio
     * @psalm-param positive-int|numeric-string $amount
     * @psalm-param positive-int|0 $expectedAmount
     *
     * @dataProvider convertExamples
     * @test
     */
    public function itConvertsToADifferentCurrencyWhenDecimalSeparatorIsComma(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount
    ): void {
        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');

        $this->itConvertsToADifferentCurrency(
            $baseCurrencyCode,
            $counterCurrencyCode,
            $subunitBase,
            $subunitCounter,
            $ratio,
            $amount,
            $expectedAmount
        );
    }

    /**
     * @psalm-return non-empty-list<array{
     *     non-empty-string,
     *     non-empty-string,
     *     positive-int|0,
     *     positive-int|0,
     *     int|float,
     *     positive-int|numeric-string,
     *     positive-int|0
     * }>
     */
    public function convertExamples(): array
    {
        return [
            ['USD', 'JPY', 2, 0, 101, 100, 101],
            ['JPY', 'USD', 0, 2, 0.0099, 1000, 990],
            ['USD', 'EUR', 2, 2, 0.89, 100, 89],
            ['EUR', 'USD', 2, 2, 1.12, 100, 112],
            ['XBT', 'USD', 8, 2, 6597, 1, 0],
            ['XBT', 'USD', 8, 2, 6597, 10, 0],
            ['XBT', 'USD', 8, 2, 6597, 100, 1],
            ['ETH', 'EUR', 18, 2, 330.84, '100000000000000000', 3308],
            ['BTC', 'USD', 8, 2, 13500, 100000000, 1350000],
            ['USD', 'BTC', 2, 8, 1 / 13500, 1350000, 100000000],
        ];
    }
}
