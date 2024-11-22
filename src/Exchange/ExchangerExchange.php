<?php

declare(strict_types=1);

namespace Money\Exchange;

use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\CurrencyPair as ExchangerCurrencyPair;
use Exchanger\Exception\Exception as ExchangerException;
use Exchanger\ExchangeRateQuery;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;

use function sprintf;

/**
 * Provides a way to get exchange rate from a third-party source and return a currency pair.
 */
final class ExchangerExchange implements Exchange
{
    public function __construct(private readonly ExchangeRateProvider $exchanger)
    {
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            $query = new ExchangeRateQuery(
                new ExchangerCurrencyPair($baseCurrency->getCode(), $counterCurrency->getCode())
            );
            $rate  = $this->exchanger->getExchangeRate($query);
        } catch (ExchangerException) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        $rateValue = sprintf('%.14F', $rate->getValue());

        return new CurrencyPair($baseCurrency, $counterCurrency, $rateValue);
    }
}
