<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Contracts;

/** @see http://en.wikipedia.org/wiki/Currency_pair */
interface CurrencyPairInterface
{
    /**
     * @param \Money\Contracts\CurrencyInterface $baseCurrency
     * @param \Money\Contracts\CurrencyInterface $counterCurrency
     * @param float                               $ratio
     * @throws \Money\InvalidArgumentException
     */
    public function __construct(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency, $ratio);

    /**
     * @param  string $iso String representation of the form "EUR/USD 1.2500"
     * @throws \Exception
     * @return \Money\Contracts\CurrencyPairInterface
     */
    public static function createFromIso($iso);

    /**
     * @param \Money\Contracts\MoneyInterface $money
     * @throws InvalidArgumentException
     * @return \Money\Contracts\MoneyInterface
     */
    public function convert(MoneyInterface $money);

    /** @return \Money\Contracts\CurrencyInterface */
    public function getCounterCurrency();

    /** @return \Money\Contracts\CurrencyInterface */
    public function getBaseCurrency();

    /** @return float */
    public function getRatio();
}