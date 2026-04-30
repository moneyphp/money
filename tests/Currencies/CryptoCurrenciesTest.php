<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use Money\Currencies\CryptoCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_map;

/** @covers \Money\Currencies\CryptoCurrencies */
final class CryptoCurrenciesTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itHasIsoCurrencies(string $currency): void
    {
        $currencies = new CryptoCurrencies();

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
        $currencies = new CryptoCurrencies();

        self::assertIsInt($currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingSubunitAndCurrencyIsUnknown(): void
    {
        $this->expectException(UnknownCurrencyException::class);

        $currencies = new CryptoCurrencies();

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function itIsIterable(): void
    {
        $currencies = new CryptoCurrencies();

        $iterator = $currencies->getIterator();

        self::assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @phpstan-return non-empty-list<array{non-empty-string}>
     */
    public static function currencyCodeExamples(): array
    {
        /** @phpstan-var non-empty-array<non-empty-string, array> $currencies */
        $currencies = require __DIR__ . '/../../resources/binance.php';

        return array_map(static function (string $currency) {
            return [$currency];
        }, array_keys($currencies));
    }
}
