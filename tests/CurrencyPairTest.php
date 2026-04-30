<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Currency;
use Money\CurrencyPair;
use PHPUnit\Framework\TestCase;

use function json_encode;

/** @covers \Money\CurrencyPair */
final class CurrencyPairTest extends TestCase
{
    /**
     * @test
     */
    public function itProvidesGetters(): void
    {
        $pair = new CurrencyPair(
            new Currency('USD'),
            new Currency('EUR'),
            '1.0'
        );

        self::assertEquals('USD', $pair->getBaseCurrency()->getCode());
        self::assertEquals('EUR', $pair->getCounterCurrency()->getCode());
        self::assertEquals('1.0', $pair->getConversionRatio());
    }

    /**
     * @test
     */
    public function itProvidesEquality(): void
    {
        $pair1 = new CurrencyPair(
            new Currency('USD'),
            new Currency('EUR'),
            '1.0'
        );

        self::assertTrue($pair1->equals(new CurrencyPair(
            new Currency('USD'),
            new Currency('EUR'),
            '1.0'
        )));
        self::assertFalse($pair1->equals(new CurrencyPair(
            new Currency('USD'),
            new Currency('EUR'),
            '2.0'
        )));
    }

    /**
     * @test
     */
    public function itConvertsToJson(): void
    {
        $pair = new CurrencyPair(
            new Currency('USD'),
            new Currency('EUR'),
            '1.0'
        );

        self::assertEquals('{"baseCurrency":"USD","counterCurrency":"EUR","ratio":"1.0"}', json_encode($pair));
    }

    /**
     * @test
     */
    public function itCanBeCreatedWithAnIsoString(): void
    {
        $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');
        self::assertEquals('EUR', $pair->getBaseCurrency()->getCode());
        self::assertEquals('USD', $pair->getCounterCurrency()->getCode());
        self::assertEquals('1.2500', $pair->getConversionRatio());
    }
}
