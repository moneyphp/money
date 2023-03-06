<?php

declare(strict_types=1);

namespace Money\Exchange;

use InvalidArgumentException;
use Money\Currency;

interface CurrencyPair
{
    /**
     * Creates a new Currency Pair based on "EUR/USD 1.2500" form representation.
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500"
     *
     * @throws InvalidArgumentException Format of $iso is invalid.
     */
    public static function createFromIso(string $iso): self;

    /**
     * Returns the conversion ratio.
     *
     * @psalm-return numeric-string
     */
    public function getConversionRatio(): string;

    /**
     * Returns the counter currency.
     */
    public function getCounterCurrency(): Currency;

    /**
     * Returns the base currency.
     */
    public function getBaseCurrency(): Currency;

    /**
     * Checks if an other CurrencyPair has the same parameters as this.
     */
    public function equals(CurrencyPair $other): bool;
}
