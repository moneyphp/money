<?php

declare(strict_types=1);

namespace Tests\Money\Exchange;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\FixedExchange;
use Money\Exchange\IndirectExchange;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Exchange\IndirectExchange */
final class IndirectExchangeTest extends TestCase
{
    /**
     * @test
     */
    public function itCalculatesAMinimalChain(): void
    {
        $exchange = $this->createExchange();

        $pair = $exchange->quote(new Currency('USD'), new Currency('AOA'));

        // USD => EUR => AOA
        self::assertEquals('USD', $pair->getBaseCurrency()->getCode());
        self::assertEquals('AOA', $pair->getCounterCurrency()->getCode());
        self::assertEquals(12, $pair->getConversionRatio());
    }

    private function createExchange(): IndirectExchange
    {
        $baseExchange = new FixedExchange([
            'USD' => [
                'AFN' => '2',
                'EUR' => '4',
            ],
            'AFN' => [
                'DZD' => '12',
                'EUR' => '8',
            ],
            'EUR' => ['AOA' => '3'],
            'DZD' => [
                'AOA' => '5',
                'USD' => '2',
            ],
            'ARS' => ['AOA' => '2'],
        ]);

        return new IndirectExchange($baseExchange, new ISOCurrencies());
    }

    /**
     * @test
     */
    public function itCalculatesAdjacentNodes(): void
    {
        $exchange = $this->createExchange();

        $pair = $exchange->quote(new Currency('USD'), new Currency('EUR'));

        self::assertEquals('USD', $pair->getBaseCurrency()->getCode());
        self::assertEquals('EUR', $pair->getCounterCurrency()->getCode());
        self::assertEquals(4, $pair->getConversionRatio());
    }

    /**
     * @test
     */
    public function itThrowsWhenNoChainIsFound(): void
    {
        $exchange = $this->createExchange();

        $this->expectException(UnresolvableCurrencyPairException::class);

        $exchange->quote(new Currency('USD'), new Currency('ARS'));
    }
}
