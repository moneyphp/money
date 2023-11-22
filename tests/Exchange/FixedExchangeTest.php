<?php

declare(strict_types=1);

namespace Exchange;

use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\FixedExchange;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Exchange\FixedExchange */
final class FixedExchangeTest extends TestCase
{
    /** @test */
    public function it_exchanges_currencies(): void
    {
        $exchange     = new FixedExchange(['EUR' => ['USD' => '1.2500']]);
        $currencyPair = $exchange->quote(new Currency('EUR'), new Currency('USD'));

        self::assertEquals('EUR', $currencyPair->getBaseCurrency());
        self::assertEquals('USD', $currencyPair->getCounterCurrency());
        self::assertEquals('1.2500', $currencyPair->getConversionRatio());
    }

    /** @test */
    public function it_throws_when_quoting_unknown_currency(): void
    {
        $this->expectException(UnresolvableCurrencyPairException::class);
        $exchange = new FixedExchange(['EUR' => ['USD' => '1.2500']]);
        $exchange->quote(new Currency('EUR'), new Currency('SOME'));
    }
}
