<?php

/**
 * This file is part of the Money library.
 *
 * Copyright (c) 2011-2014 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use InvalidArgumentException;

/**
 * Currency Pair
 *
 * @author Mathias Verraes
 * @see http://en.wikipedia.org/wiki/Currency_pair
 */
class CurrencyPair
{
    /**
     * Base currency
     *
     * Currency to convert from
     *
     * @var Currency
     */
    private $baseCurrency;

    /**
     * Counter currency
     *
     * Currency to convert to
     *
     * @var Currency
     */
    private $counterCurrency;

    /**
     * Conversion ratio
     *
     * @var float
     */
    private $ratio;

    /**
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     * @param float    $ratio
     *
     * @throws InvalidArgumentException If ratio is not numeric
     */
    public function __construct(Currency $baseCurrency, Currency $counterCurrency, $ratio)
    {
        if(!is_numeric($ratio)) {
            throw new InvalidArgumentException("Ratio must be numeric");
        }

        $this->counterCurrency = $counterCurrency;
        $this->baseCurrency = $baseCurrency;
        $this->ratio = (float) $ratio;
    }

    /**
     * Creates a new Currency Pair based on "EUR/USD 1.2500" form representation
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500"
     *
     * @return CurrencyPair
     *
     * @throws InvalidArgumentException Format of $iso is invalid
     */
    public static function createFromIso($iso)
    {
        $currency = "([A-Z]{2,3})";
        $ratio = "([0-9]*\.?[0-9]+)"; // @see http://www.regular-expressions.info/floatingpoint.html
        $pattern = '#'.$currency.'/'.$currency.' '.$ratio.'#';

        $matches = array();
        if (!preg_match($pattern, $iso, $matches)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Can't create currency pair from ISO string '%s', format of string is invalid",
                    $iso
                )
            );
        }

        return new static(new Currency($matches[1]), new Currency($matches[2]), $matches[3]);
    }

    /**
     * Converts Money from base to counter currency
     *
     * @param Money   $money
     * @param integer $rounding_mode
     *
     * @return Money
     *
     * @throws InvalidArgumentException If $money's currency is not equal to base currency
     */
    public function convert(Money $money, RoundingMode $rounding_mode = null)
    {
        if (!$money->getCurrency()->equals($this->baseCurrency)) {
            throw new InvalidArgumentException("The Money has the wrong currency");
        }

        $rounding_mode = $rounding_mode ?: RoundingMode::halfUp();

        return new Money(
            (int) round($money->getAmount() * $this->ratio, 0, $rounding_mode->getRoundingMode()),
            $this->counterCurrency
        );
    }

    /**
     * Returns the counter currency
     *
     * @return Currency
     */
    public function getCounterCurrency()
    {
        return $this->counterCurrency;
    }

    /**
     * Returns the base currency
     *
     * @return Currency
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Returns the conversion ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * Checks if an other CurrencyPair has the same parameters as this
     *
     * @param CurrencyPair $other
     *
     * @return boolean
     */
    public function equals(CurrencyPair $other)
    {
        return
            $this->baseCurrency->equals($other->baseCurrency)
            && $this->counterCurrency->equals($other->counterCurrency)
            && $this->ratio === $other->ratio
        ;
    }
}
