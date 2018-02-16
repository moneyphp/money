<?php

namespace Tests\Money\Exchange;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\FixedExchange;
use Money\Exchange\IndirectExchange;
use PHPUnit\Framework\TestCase;

final class IndirectExchangeTest extends TestCase
{
    /**
     * @test
     */
    public function it_calculates_a_minimal_chain()
    {
        $exchange = $this->createExchange();

        $pair = $exchange->quote(new Currency('USD'), new Currency('AOA'));

        // USD => EUR => AOA
        $this->assertEquals('USD', $pair->getBaseCurrency()->getCode());
        $this->assertEquals('AOA', $pair->getCounterCurrency()->getCode());
        $this->assertEquals(12, $pair->getConversionRatio());
    }

    private function createExchange()
    {
        $baseExchange = new FixedExchange([
            'USD' => [
                'AFN' => 2,
                'EUR' => 4,
            ],
            'AFN' => [
                'DZD' => 12,
                'EUR' => 8,
            ],
            'EUR' => [
                'AOA' => 3,
            ],
            'DZD' => [
                'AOA' => 5,
                'USD' => 2,
            ],
            'ARS' => [
                'AOA' => 2,
            ],
        ]);

        return new IndirectExchange($baseExchange, new ISOCurrencies());
    }

    /**
     * @test
     */
    public function it_calculates_adjacent_nodes()
    {
        $exchange = $this->createExchange();

        $pair = $exchange->quote(new Currency('USD'), new Currency('EUR'));

        $this->assertEquals('USD', $pair->getBaseCurrency()->getCode());
        $this->assertEquals('EUR', $pair->getCounterCurrency()->getCode());
        $this->assertEquals(4, $pair->getConversionRatio());
    }

    /**
     * @test
     */
    public function it_throws_when_no_chain_is_found()
    {
        $exchange = $this->createExchange();

        $this->expectException(UnresolvableCurrencyPairException::class);

        $exchange->quote(new Currency('USD'), new Currency('ARS'));
    }
}
