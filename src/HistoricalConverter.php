<?php

namespace Money;

use DateTimeInterface;

/**
 * Provides a way to convert Money to Money in another Currency using an exchange rate at a certain date.
 *
 * @author Stefan Doorn <stefan@efectos.nl>
 */
final class HistoricalConverter
{
    /**
     * @var Currencies
     */
    private $currencies;

    /**
     * @var HistoricalExchange
     */
    private $exchange;

    /**
     * @param Currencies         $currencies
     * @param HistoricalExchange $exchange
     */
    public function __construct(Currencies $currencies, HistoricalExchange $exchange)
    {
        $this->currencies = $currencies;
        $this->exchange = $exchange;
    }

    /**
     * @param Money             $money
     * @param Currency          $counterCurrency
     * @param DateTimeInterface $date
     * @param int               $roundingMode
     *
     * @return Money
     */
    public function convert(Money $money, Currency $counterCurrency, DateTimeInterface $date, $roundingMode = Money::ROUND_HALF_UP)
    {
        $baseCurrency = $money->getCurrency();
        $ratio = $this->exchange->historical($baseCurrency, $counterCurrency, $date)->getConversionRatio();

        $baseCurrencySubunit = $this->currencies->subunitFor($baseCurrency);
        $counterCurrencySubunit = $this->currencies->subunitFor($counterCurrency);
        $subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

        $ratio = $ratio / pow(10, $subunitDifference);

        $counterValue = $money->multiply($ratio, $roundingMode);

        return new Money($counterValue->getAmount(), $counterCurrency);
    }
}
