<?php

declare(strict_types=1);

namespace Money\Exchange;

use Exchanger\Exception\Exception as ExchangerException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Swap\Swap;

use function sprintf;

/**
 * Provides a way to get exchange rate from a third-party source and return a currency pair.
 */
final class SwapExchange implements Exchange
{
    public function __construct(private readonly Swap $swap)
    {
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            $rate = $this->swap->latest($baseCurrency->getCode() . '/' . $counterCurrency->getCode());
        } catch (ExchangerException) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        $rateValue = sprintf('%.14F', $rate->getValue());

        return new CurrencyPair($baseCurrency, $counterCurrency, $rateValue);
    }
}
