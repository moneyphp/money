<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_map;

/** @covers \Money\Currencies\ISOCurrencies */
final class ISOCurrenciesTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itHasIsoCurrencies(string $currency): void
    {
        $currencies = new ISOCurrencies();

        self::assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @phpstan-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesSubunit(string $currency): void
    {
        $currencies = new ISOCurrencies();

        self::assertIsInt($currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingSubunitAndCurrencyIsUnknown(): void
    {
        $this->expectException(UnknownCurrencyException::class);

        $currencies = new ISOCurrencies();

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @phpstan-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesNumericCode(string $currency): void
    {
        $currencies = new ISOCurrencies();

        self::assertIsInt($currencies->numericCodeFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingNumericCodeAndCurrencyIsUnknown(): void
    {
        $this->expectException(UnknownCurrencyException::class);

        $currencies = new ISOCurrencies();

        $currencies->numericCodeFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function itIsIterable(): void
    {
        $currencies = new ISOCurrencies();

        $iterator = $currencies->getIterator();

        self::assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @phpstan-return non-empty-list<array{non-empty-string}>
     */
    public static function currencyCodeExamples(): array
    {
        /** @phpstan-var non-empty-array<non-empty-string, array> $currencies */
        $currencies = require __DIR__ . '/../../resources/currency.php';

        return array_map(static function (string $currency) {
            return [$currency];
        }, array_keys($currencies));
    }
}
