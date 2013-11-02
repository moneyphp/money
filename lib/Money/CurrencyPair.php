<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use Money\Contracts\CurrencyPairInterface;
use Money\Contracts\CurrencyInterface;
use Money\Contracts\MoneyInterface;

/** @see http://en.wikipedia.org/wiki/Currency_pair */
class CurrencyPair implements CurrencyPairInterface
{
    /** @var Currency */
    private $baseCurrency;

    /** @var Currency */
    private $counterCurrency;

    /** @var float */
    private $ratio;

    /**
     * @{inheritDoc}
     */
    public function __construct(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency, $ratio)
    {
        if(!is_numeric($ratio)) {
            throw new InvalidArgumentException("Ratio must be numeric");
        }

        $this->counterCurrency = $counterCurrency;
        $this->baseCurrency = $baseCurrency;
        $this->ratio = (float) $ratio;
    }

    /**
     * @{inheritDoc}
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
     * @{inheritDoc}
     */
    public function convert(MoneyInterface $money)
    {
        if (!$money->getCurrency()->equals($this->baseCurrency)) {
            throw new InvalidArgumentException("The Money has the wrong currency");
        }

        // @todo add rounding mode?
        return new Money((int) round($money->getAmount() * $this->ratio), $this->counterCurrency);
    }

    /**
     * @{inheritDoc}
     */
    public function getCounterCurrency()
    {
        return $this->counterCurrency;
    }

    /**
     * @{inheritDoc}
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * @{inheritDoc}
     */
    public function getRatio()
    {
        return $this->ratio;
    }
}