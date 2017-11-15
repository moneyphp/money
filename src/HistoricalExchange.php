<?php

namespace Money;

use Money\Exception\UnresolvableCurrencyPairException;
use DateTimeInterface;

/**
 * Provides a way to get exchange rate from a third-party source at a certain moment in history and return a currency pair.
 *
 * @author Stefan Doorn <stefan@efectos.nl>
 */
interface HistoricalExchange
{
    /**
     * Returns a currency pair for the passed currencies with the rate coming from a third-party source at a certain date.
     *
     * @param Currency          $baseCurrency
     * @param Currency          $counterCurrency
     * @param DateTimeInterface $date
     *
     * @return CurrencyPair
     *
     * @throws UnresolvableCurrencyPairException When there is no currency pair (rate) available for the given currencies
     */
    public function historical(Currency $baseCurrency, Currency $counterCurrency, DateTimeInterface $date);
}
