<?php

declare(strict_types=1);

namespace Tests\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\CurrencyPair as ExchangerCurrencyPair;
use Exchanger\Exception\Exception;
use Exchanger\ExchangeRateQuery;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\ExchangerExchange;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Exchange\ExchangerExchange */
final class ExchangerExchangeTest extends TestCase
{
    /** @test */
    public function it_exchanges_currencies(): void
    {
        $exchangeRate  = $this->createMock(ExchangeRate::class);
        $exchangeRates = $this->createMock(ExchangeRateProvider::class);

        $exchangeRate->method('getValue')
            ->willReturn(1.12);
        $exchangeRates->method('getExchangeRate')
            ->with(self::equalTo(new ExchangeRateQuery(new ExchangerCurrencyPair('EUR', 'USD'))))
            ->willReturn($exchangeRate);

        $base         = new Currency('EUR');
        $counter      = new Currency('USD');
        $currencyPair = (new ExchangerExchange($exchangeRates))
            ->quote($base, $counter);

        self::assertEquals($base, $currencyPair->getBaseCurrency());
        self::assertEquals($counter, $currencyPair->getCounterCurrency());
        self::assertEquals('1.12000000000000', $currencyPair->getConversionRatio());
    }

    /** @test */
    public function it_throws_an_exception_when_cannot_exchange_currencies(): void
    {
        $exchangeRates = $this->createMock(ExchangeRateProvider::class);

        $exchangeRates->method('getExchangeRate')
            ->willThrowException(new Exception());

        $exchanger = new ExchangerExchange($exchangeRates);

        $this->expectException(UnresolvableCurrencyPairException::class);
        $exchanger->quote(new Currency('EUR'), new Currency('XYZ'));
    }
}
