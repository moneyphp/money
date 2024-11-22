<?php

declare(strict_types=1);

namespace Tests\Money;

use InvalidArgumentException;
use Money\Converter;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use PHPUnit\Framework\TestCase;

use function sprintf;

use const LC_ALL;

/** @covers \Money\Converter */
final class ConverterTest extends TestCase
{
    use Locale;

    /**
     * @phpstan-param non-empty-string $baseCurrencyCode
     * @phpstan-param non-empty-string $counterCurrencyCode
     * @phpstan-param non-negative-int $subunitBase
     * @phpstan-param non-negative-int $subunitCounter
     * @phpstan-param int|float $ratio
     * @phpstan-param positive-int|numeric-string $amount
     * @phpstan-param non-negative-int $expectedAmount
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
        $numericRatio    =  sprintf('%.14F', $ratio);

        self::assertIsNumeric($numericRatio);

        $pair       = new CurrencyPair($baseCurrency, $counterCurrency, $numericRatio);
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

        self::assertEquals($expectedAmount, $money->getAmount());
        self::assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    /**
     * @phpstan-param non-empty-string $baseCurrencyCode
     * @phpstan-param non-empty-string $counterCurrencyCode
     * @phpstan-param non-negative-int $subunitBase
     * @phpstan-param non-negative-int $subunitCounter
     * @phpstan-param int|float $ratio
     * @phpstan-param positive-int|numeric-string $amount
     * @phpstan-param non-negative-int $expectedAmount
     *
     * @dataProvider convertExamples
     * @test
     */
    public function itConvertsAndReturnWithCurrencyPair(
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
        $numericRatio    =  sprintf('%.14F', $ratio);

        self::assertIsNumeric($numericRatio);

        $pair       = new CurrencyPair($baseCurrency, $counterCurrency, $numericRatio);
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

        [$money, $currencyPair] = $converter->convertAndReturnWithCurrencyPair(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency
        );

        self::assertEquals($expectedAmount, $money->getAmount());
        self::assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
        self::assertEquals($baseCurrencyCode, $currencyPair->getBaseCurrency()->getCode());
        self::assertEquals($counterCurrencyCode, $currencyPair->getCounterCurrency()->getCode());
    }

    /**
     * @phpstan-param non-empty-string $baseCurrencyCode
     * @phpstan-param non-empty-string $counterCurrencyCode
     * @phpstan-param non-negative-int $subunitBase
     * @phpstan-param non-negative-int $subunitCounter
     * @phpstan-param int|float $ratio
     * @phpstan-param positive-int|numeric-string $amount
     * @phpstan-param non-negative-int $expectedAmount
     *
     * @dataProvider convertExamples
     * @test
     */
    public function itConvertsAgainstCurrencyPair(
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
        $numericRatio    = sprintf('%.14F', $ratio);

        self::assertIsNumeric($numericRatio);

        $pair       = new CurrencyPair($baseCurrency, $counterCurrency, $numericRatio);
        $currencies = $this->createMock(Currencies::class);
        $exchange   = $this->createMock(Exchange::class);
        $converter  = new Converter($currencies, $exchange);

        $currencies->method('subunitFor')
            ->with(self::logicalOr(self::equalTo($baseCurrency), self::equalTo($counterCurrency)))
            ->willReturnCallback(
                static fn (Currency $currency): int => $currency->equals($baseCurrency) ? $subunitBase : $subunitCounter
            );

        $money = $converter->convertAgainstCurrencyPair(
            new Money($amount, new Currency($baseCurrencyCode)),
            $pair
        );

        self::assertEquals($expectedAmount, $money->getAmount());
        self::assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    /**
     * @phpstan-param non-empty-string $baseCurrencyCode
     * @phpstan-param non-empty-string $counterCurrencyCode
     * @phpstan-param non-negative-int $subunitBase
     * @phpstan-param non-negative-int $subunitCounter
     * @phpstan-param int|float $ratio
     * @phpstan-param positive-int|numeric-string $amount
     * @phpstan-param non-negative-int $expectedAmount
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
        self::runLocaleAware(LC_ALL, 'ru_RU.UTF-8', function () use ($expectedAmount, $amount, $ratio, $subunitCounter, $subunitBase, $counterCurrencyCode, $baseCurrencyCode): void {
            $this->itConvertsToADifferentCurrency(
                $baseCurrencyCode,
                $counterCurrencyCode,
                $subunitBase,
                $subunitCounter,
                $ratio,
                $amount,
                $expectedAmount
            );
        });
    }

    /**
     * @test
     */
    public function itThrowsWhenConvertingAgainstTheWrongBaseCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $pair       = new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1');
        $currencies = $this->createMock(Currencies::class);
        $exchange   = $this->createMock(Exchange::class);
        $converter  = new Converter($currencies, $exchange);

        $converter->convertAgainstCurrencyPair(new Money(100, new Currency('XYZ')), $pair);
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     non-empty-string,
     *     non-empty-string,
     *     non-negative-int,
     *     non-negative-int,
     *     int|float,
     *     positive-int|numeric-string,
     *     non-negative-int
     * }>
     */
    public static function convertExamples(): array
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
