<?php
/**
 * This file is part of the Verraes\Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Verraes\Money;

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
		$this->counterCurrency = $counterCurrency;
		$this->baseCurrency = $baseCurrency;
		$this->ratio = $ratio;
	}

	/** @return Money */
	public function convert(Money $money)
	{
		if(!$money->getCurrency()->equals($this->counterCurrency)) {
			throw new InvalidArgumentException("The Money has the wrong currency");
		}

		// @todo add rounding mode?
		return new Money((int) round($money->getUnits() * $this->ratio), $this->baseCurrency);
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