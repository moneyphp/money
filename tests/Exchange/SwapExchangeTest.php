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

/** @covers \Money\Exchange\SwapExchange */
final class SwapExchangeTest extends TestCase
{
    /**
     * @phpstan-return non-empty-list<array{
     *     float,
     *     numeric-string
     * }>
     */
    public static function exchangeRateExamples(): array
    {
        return [
            [1.25, '1.25000000000000'],
            [0.0000550000, '0.00005500000000'],
            [1.4E-5, '0.00001400000000'],
        ];
    }

    /**
     * @phpstan-param float $exchangeRateValue
     * @phpstan-param numeric-string $expectedConversionRatio
     *
     * @dataProvider exchangeRateExamples
     * @test
     */
    public function it_exchanges_currencies(float $exchangeRateValue, string $expectedConversionRatio): void
    {
        $base         = new Currency('EUR');
        $counter      = new Currency('USD');
        $swapExchange = $this->createMock(Swap::class);
        $exchangeRate = $this->createMock(ExchangeRate::class);

        $exchangeRate->method('getValue')
            ->willReturn($exchangeRateValue);

        $swapExchange->expects(self::once())
            ->method('latest')
            ->with('EUR/USD')
            ->willReturn($exchangeRate);

        self::assertEquals(
            new CurrencyPair($base, $counter, $expectedConversionRatio),
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
