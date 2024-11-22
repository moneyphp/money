<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use Money\Currencies\CurrencyList;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_map;

/** @covers \Money\Currencies\CurrencyList */
final class CurrencyListTest extends TestCase
{
    private const CORRECT_CURRENCIES = [
        'MY1' => 2,
        'MY2' => 0,
        'MY3' => 1,
    ];

    /**
     * @phpstan-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itHasCurrencies(string $currency): void
    {
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

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
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

        self::assertIsInt($currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingSubunitAndCurrencyIsUnknown(): void
    {
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

        $this->expectException(UnknownCurrencyException::class);

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function itIsIterable(): void
    {
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

        $iterator = $currencies->getIterator();

        self::assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /** @phpstan-return non-empty-list<array{non-empty-string}> */
    public static function currencyCodeExamples(): array
    {
        $currencies = array_keys(self::CORRECT_CURRENCIES);

        return array_map(static function ($currency) {
            return [$currency];
        }, $currencies);
    }
}
