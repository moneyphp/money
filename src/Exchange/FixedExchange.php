<?php

declare(strict_types=1);

namespace Money\Exchange;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;

/**
 * Provides a way to get exchange rate from a static list (array).
 */
final class FixedExchange implements Exchange
{
    /** @phpstan-param array<non-empty-string, array<non-empty-string, numeric-string>> $list */
    public function __construct(private readonly array $list)
    {
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        if (isset($this->list[$baseCurrency->getCode()][$counterCurrency->getCode()])) {
            return new CurrencyPair(
                $baseCurrency,
                $counterCurrency,
                $this->list[$baseCurrency->getCode()][$counterCurrency->getCode()]
            );
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }
}
