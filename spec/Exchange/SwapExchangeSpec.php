<?php

declare(strict_types=1);

namespace spec\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Exception\Exception;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\SwapExchange;
use PhpSpec\ObjectBehavior;
use Swap\Swap;

final class SwapExchangeSpec extends ObjectBehavior
{
    public function let(Swap $swap): void
    {
        $this->beConstructedWith($swap);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SwapExchange::class);
    }

    public function it_is_an_exchange(): void
    {
        $this->shouldImplement(Exchange::class);
    }

    public function it_exchanges_currencies(Swap $swap, ExchangeRate $exchangeRate): void
    {
        $exchangeRate->getValue()->willReturn('1.0');

        $swap->latest('EUR/USD')->willReturn($exchangeRate);

        $currencyPair = $this->quote($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn('1');
    }

    public function it_throws_an_exception_when_cannot_exchange_currencies(Swap $swap): void
    {
        $swap->latest('EUR/XYZ')->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('EUR'), new Currency('XYZ'));
    }
}
