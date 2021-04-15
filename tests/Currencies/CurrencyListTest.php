<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use InvalidArgumentException;
use Money\Currencies\CurrencyList;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_map;

final class CurrencyListTest extends TestCase
{
    private const CORRECT_CURRENCIES = [
        'MY1' => 2,
        'MY2' => 0,
        'MY3' => 1,
    ];

    /**
     * @psalm-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itHasCurrencies(string $currency): void
    {
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @psalm-param non-empty-string $currency
     *
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesSubunit(string $currency): void
    {
        $currencies = new CurrencyList(self::CORRECT_CURRENCIES);

        $this->assertIsInt($currencies->subunitFor(new Currency($currency)));
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

        $this->assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @psalm-param array<(positive-int|string), (int|array|float|null)> $currencies
     *
     * @dataProvider invalidInstantiation
     * @test
     */
    public function itDoesNotInitializeIfArrayIsInvalid(array $currencies): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CurrencyList($currencies);
    }

    /** @psalm-return non-empty-list<array{non-empty-string}> */
    public function currencyCodeExamples(): array
    {
        $currencies = array_keys(self::CORRECT_CURRENCIES);

        return array_map(static function ($currency) {
            return [$currency];
        }, $currencies);
    }

    /** @psalm-return non-empty-list<array{array<(positive-int|string), (int|array|float|null)>}> */
    public function invalidInstantiation(): array
    {
        return [
            [[1 => 2]],
            [['' => 2]],
            [['OWO' => []]],
            [['OWO' => null]],
            [['OWO' => '']],
            [['OWO' => -2]],
            [['OWO' => 2.1]],
        ];
    }
}
