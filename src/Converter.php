<?php

declare(strict_types=1);

namespace Money;

use InvalidArgumentException;

use function sprintf;

/**
 * Provides a way to convert Money to Money in another Currency using an exchange rate.
 */
final class Converter
{
    public function __construct(private readonly Currencies $currencies, private readonly Exchange $exchange)
    {
    }

    /**
     * @param Money::ROUND_* $roundingMode
     */
    public function convert(Money $money, Currency $counterCurrency, int $roundingMode = Money::ROUND_HALF_UP): Money
    {
        return $this->convertAgainstCurrencyPair(
            $money,
            $this->exchange->quote(
                $money->getCurrency(),
                $counterCurrency
            ),
            $roundingMode
        );
    }

    /**
     * @param Money::ROUND_* $roundingMode
     *
     * @return array{0: Money, 1: CurrencyPair}
     */
    public function convertAndReturnWithCurrencyPair(Money $money, Currency $counterCurrency, int $roundingMode = Money::ROUND_HALF_UP): array
    {
        $pair = $this->exchange->quote(
            $money->getCurrency(),
            $counterCurrency
        );

        return [$this->convertAgainstCurrencyPair($money, $pair, $roundingMode), $pair];
    }

    /**
     * @param Money::ROUND_* $roundingMode
     */
    public function convertAgainstCurrencyPair(Money $money, CurrencyPair $currencyPair, int $roundingMode = Money::ROUND_HALF_UP): Money
    {
        if (! $money->getCurrency()->equals($currencyPair->getBaseCurrency())) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expecting to convert against base currency %s, but got %s instead',
                    $money->getCurrency()->getCode(),
                    $currencyPair->getBaseCurrency()->getCode()
                )
            );
        }

        $ratio                  = $currencyPair->getConversionRatio();
        $baseCurrencySubunit    = $this->currencies->subunitFor($currencyPair->getBaseCurrency());
        $counterCurrencySubunit = $this->currencies->subunitFor($currencyPair->getCounterCurrency());
        $subunitDifference      = $baseCurrencySubunit - $counterCurrencySubunit;

        $ratio = Number::fromString($ratio)
            ->base10($subunitDifference)
            ->__toString();

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $currencyPair->getCounterCurrency());
    }
}
