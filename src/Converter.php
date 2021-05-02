<?php

declare(strict_types=1);

namespace Money;

/**
 * Provides a way to convert Money to Money in another Currency using an exchange rate.
 */
final class Converter
{
    private Currencies $currencies;

    private Exchange $exchange;

    public function __construct(Currencies $currencies, Exchange $exchange)
    {
        $this->currencies = $currencies;
        $this->exchange   = $exchange;
    }

    public function convert(Money $money, Currency $counterCurrency, int $roundingMode = Money::ROUND_HALF_UP): Money
    {
        $baseCurrency = $money->getCurrency();
        $ratio        = $this->exchange->quote($baseCurrency, $counterCurrency)->getConversionRatio();

        $baseCurrencySubunit    = $this->currencies->subunitFor($baseCurrency);
        $counterCurrencySubunit = $this->currencies->subunitFor($counterCurrency);
        $subunitDifference      = $baseCurrencySubunit - $counterCurrencySubunit;

        $ratio = Number::fromString($ratio)
            ->base10($subunitDifference)
            ->__toString();

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $counterCurrency);
    }
}
