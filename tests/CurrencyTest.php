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
}
