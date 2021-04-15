<?php

declare(strict_types=1);

namespace spec\Money\Exchange;

use Exchanger\Contract\ExchangeRate;
use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\CurrencyPair as ExchangerCurrencyPair;
use Exchanger\Exception\Exception;
use Exchanger\ExchangeRateQuery;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use PhpSpec\Exception\Example\PendingException;
use PhpSpec\ObjectBehavior;

final class ExchangerExchangeSpec extends ObjectBehavior
{
    public function it_exchanges_currencies(ExchangeRateProvider $exchanger, ExchangeRate $exchangeRate): void
    {
        throw new PendingException('Test was incorrectly formulated, and needs to be re-written');

        $exchangeRate->getValue()->willReturn('1.0');

        $query = new ExchangeRateQuery(new ExchangerCurrencyPair('EUR', 'USD'));
        $exchanger->getExchangeRate($query)->willReturn($exchangeRate);

        $currencyPair = $this->quote($base = new Currency('EUR'), $counter = new Currency('USD'));

        $currencyPair->shouldHaveType(CurrencyPair::class);
        $currencyPair->getBaseCurrency()->shouldReturn($base);
        $currencyPair->getCounterCurrency()->shouldReturn($counter);
        $currencyPair->getConversionRatio()->shouldReturn(1.0);
    }

    public function it_throws_an_exception_when_cannot_exchange_currencies(ExchangeRateProvider $exchanger): void
    {
        throw new PendingException('Test was incorrectly formulated, and needs to be re-written');

        $query = new ExchangeRateQuery(new ExchangerCurrencyPair('EUR', 'XYZ'));
        $exchanger->getExchangeRate($query)->willThrow(Exception::class);

        $this->shouldThrow(UnresolvableCurrencyPairException::class)
            ->duringQuote(new Currency('EUR'), new Currency('XYZ'));
    }
}
