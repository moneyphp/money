<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Currency;
use PHPUnit\Framework\TestCase;

use function json_encode;

/** @covers \Money\Currency */
final class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function itConvertsToJson(): void
    {
        self::assertEquals('"USD"', json_encode(new Currency('USD')));
    }

    /**
     * @test
     */
    public function itAppliesUppercase(): void
    {
        self::assertEquals('USD', (new Currency('usd'))->getCode());
    }

    /**
     * @test
     */
    public function itIsStringable(): void
    {
        self::assertEquals('USD', (string) new Currency('usd'));
    }

    /**
     * @test
     */
    public function itProvidesEqualityComparison(): void
    {
        $currency = new Currency('usd');
        self::assertTrue($currency->equals(new Currency('USD')));
    }
}
