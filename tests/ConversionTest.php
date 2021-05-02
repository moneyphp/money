<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Conversion;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Conversion */
final class ConversionTest extends TestCase
{
    /**
     * @test
     */
    public function itIsPure(): void
    {
        $conversion = new Conversion(Money::EUR(100), new CurrencyPair(new Currency('EUR'), new Currency('USD'), '1'));

        self::assertEquals('100', $conversion->getMoney()->getAmount());
        self::assertEquals('EUR', $conversion->getCurrencyPair()->getBaseCurrency()->getCode());
        self::assertEquals('USD', $conversion->getCurrencyPair()->getCounterCurrency()->getCode());
        self::assertEquals('1', $conversion->getCurrencyPair()->getConversionRatio());
    }
}
