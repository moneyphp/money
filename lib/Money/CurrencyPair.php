<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

/** @see http://en.wikipedia.org/wiki/Currency_pair */
class CurrencyPair
{
    /** @var Currency */
    private $counterCurrency;

    /** @var Currency */
    private $baseCurrency;

    /** @var float */
    private $ratio;

    public function __construct(Currency $counterCurrency, Currency $baseCurrency, $ratio)
    {
        if(!is_numeric($ratio)) {
            throw new InvalidArgumentException("Ratio must be numeric");
        }

        $this->counterCurrency = $counterCurrency;
        $this->baseCurrency = $baseCurrency;
        $this->ratio = (float) $ratio;
    }

    /**
     * @param  string       $iso String representation of the form "EUR/USD 1.2500"
     * @return CurrencyPair
     */
    public static function createFromIso($iso)
    {
        $currency = "([A-Z]{2,3})";
        $ratio = "([0-9]*\.?[0-9]+)"; // @see http://www.regular-expressions.info/floatingpoint.html
        $pattern = '#'.$currency.'/'.$currency.' '.$ratio.'#';

        $matches = array();
        if (!preg_match($pattern, $iso, $matches)) {
            // @todo better exception
            throw new \Exception();
        }

        return new static(new Currency($matches[1]), new Currency($matches[2]), $matches[3]);
    }

    /** @return Money */
    public function convert(Money $money)
    {
        if (!$money->getCurrency()->equals($this->counterCurrency)) {
            throw new InvalidArgumentException("The Money has the wrong currency");
        }

        // @todo add rounding mode?
        return new Money((int) round($money->getAmount() * $this->ratio), $this->baseCurrency);
    }

    /** @return Currency */
    public function getCounterCurrency()
    {
        return $this->counterCurrency;
    }

    /** @return Currency */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /** @return float */
    public function getRatio()
    {
        return $this->ratio;
    }
}
