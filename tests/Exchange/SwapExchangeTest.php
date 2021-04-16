<?php

declare(strict_types=1);

namespace Tests\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception as ExchangerException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\SwapExchange;
use PHPUnit\Framework\TestCase;
use Swap\Swap;

final class SwapExchangeTest extends TestCase
{
    /** @test */
    public function it_exchanges_currencies(): void
    {
        $base         = new Currency('EUR');
        $counter      = new Currency('USD');
        $swapExchange = $this->createMock(Swap::class);
        $exchangeRate = $this->createMock(ExchangeRate::class);

        $exchangeRate->method('getValue')
            ->willReturn(1.25);

        $swapExchange->expects(self::once())
            ->method('latest')
            ->with('EUR/USD')
            ->willReturn($exchangeRate);

        self::assertEquals(
            new CurrencyPair($base, $counter, '1.25'),
            (new SwapExchange($swapExchange))
                ->quote($base, $counter)
        );
    }

    /** @test */
    public function it_throws_an_exception_when_cannot_exchange_currencies(): void
    {
        $base         = new Currency('EUR');
        $counter      = new Currency('USD');
        $swapExchange = $this->createMock(Swap::class);
        $exchangeRate = $this->createMock(ExchangeRate::class);

        $exchangeRate->method('getValue')
            ->willReturn(1.25);

        $swapExchange->expects(self::once())
            ->method('latest')
            ->with('EUR/USD')
            ->willThrowException(new ExchangerException());

        $exchanger = new SwapExchange($swapExchange);

        $this->expectException(UnresolvableCurrencyPairException::class);

        $exchanger->quote($base, $counter);
    }
}
