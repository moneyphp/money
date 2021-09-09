<?php

declare(strict_types=1);

namespace Tests\Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\ReversedCurrenciesExchange;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Exchange\ReversedCurrenciesExchange */
final class ReversedCurrenciesExchangeTest extends TestCase
{
    /** @test */
    public function it_exchanges_currencies(): void
    {
        $base            = new Currency('EUR');
        $counter         = new Currency('USD');
        $wrappedExchange = $this->createMock(Exchange::class);

        $wrappedExchange->method('quote')
            ->with(self::equalTo($base), self::equalTo($counter))
            ->willReturn(new CurrencyPair($base, $counter, '1.25'));

        self::assertEquals(
            new CurrencyPair($base, $counter, '1.25'),
            (new ReversedCurrenciesExchange($wrappedExchange))
                ->quote($base, $counter)
        );
    }

    /** @test */
    public function it_exchanges_reversed_currencies_when_the_original_pair_is_not_found(): void
    {
        $base            = new Currency('EUR');
        $counter         = new Currency('USD');
        $wrappedExchange = $this->createMock(Exchange::class);

        $wrappedExchange->method('quote')
            ->willReturnCallback(static function (Currency $givenBase, Currency $givenCounter) use ($base
            ): CurrencyPair {
                if ($givenBase->equals($base)) {
                    throw new UnresolvableCurrencyPairException();
                }

                return new CurrencyPair($givenBase, $givenCounter, '1.25');
            });

        self::assertEquals(
            new CurrencyPair($base, $counter, '0.80000000000000'),
            (new ReversedCurrenciesExchange($wrappedExchange))
                ->quote($base, $counter)
        );
    }

    /** @test */
    public function it_throws_an_exception_when_neither_the_original_nor_the_reversed_currency_pair_can_be_resolved(): void
    {
        $exception1      = new UnresolvableCurrencyPairException('first thrown');
        $exception2      = new UnresolvableCurrencyPairException('second thrown');
        $base            = new Currency('EUR');
        $counter         = new Currency('USD');
        $wrappedExchange = $this->createMock(Exchange::class);

        $wrappedExchange->method('quote')
            ->willReturnCallback(static function (Currency $givenBase) use ($exception2, $exception1, $base
            ): CurrencyPair {
                if ($givenBase->equals($base)) {
                    throw $exception1;
                }

                throw $exception2;
            });

        $exchanger = new ReversedCurrenciesExchange($wrappedExchange);

        $this->expectExceptionObject($exception1);

        $exchanger->quote($base, $counter);
    }
}
