<?php

declare(strict_types=1);

namespace spec\Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Exchange\FixedExchange;
use PhpSpec\ObjectBehavior;

final class FixedExchangeSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith([
            'EUR' => ['USD' => '1.25'],
        ]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FixedExchange::class);
    }

    public function it_is_an_exchange(): void
    {
        $this->shouldImplement(Exchange::class);
    }

    public function it_exchanges_currencies(): void
    {
        $baseCurrency    = new Currency('EUR');
        $counterCurrency = new Currency('USD');

        $currencyPair = $this->quote($baseCurrency, $counterCurrency);

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($baseCurrency);
        $currencyPair->getCounterCurrency()->shouldReturn($counterCurrency);
        $currencyPair->getConversionRatio()->shouldReturn('1.25');
    }

    public function it_cannot_exchange_currencies(): void
    {
        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('USD'), new Currency('EUR'));
    }
}
