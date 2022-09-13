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
    /** @psalm-var array<non-empty-string, array<non-empty-string, numeric-string>> */
    private array $list;

    /** @psalm-var non-empty-string  */
    private string $providerName;

    /** @psalm-param array<non-empty-string, array<non-empty-string, numeric-string>> $list */
    public function __construct(array $list, string $providerName = 'fixed')
    {
        $this->list = $list;
        $this->providerName = $providerName;
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        if (isset($this->list[$baseCurrency->getCode()][$counterCurrency->getCode()])) {
            return new CurrencyPair(
                $baseCurrency,
                $counterCurrency,
                $this->list[$baseCurrency->getCode()][$counterCurrency->getCode()],
                $this->providerName
            );
        }

        throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
    }
}
