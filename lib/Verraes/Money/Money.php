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

use Verraes\Money\InvalidArgumentException;

final class Money
{
	const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
	const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
	const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
	const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

	/**
	 * @var int
	 */
	private $units;

	/** @var Verraes\Money\Currency */
	private $currency;

	/**
	 * Create a Money instance
	 * @param integer $units Amount, expressed in the smallest units of $currency (eg cents)
	 * @param Verraes\Money\Currency $currency
	 * @throws Verraes\Money\InvalidArgumentException
	 */
	public function __construct($units, Currency $currency)
	{
		if(!is_int($units)) {
			throw new InvalidArgumentException("The first parameter of Money must be an integer");
		}
		$this->units = $units;
		$this->currency = $currency;
	}

	/**
	 * Convenience factory method for an amount in EURO
	 * @return Money
	 */
	public static function euro($units)
	{
		return new Money($units, new Euro);
	}

	/**
	 * Convenience factory method for an amount in USD
	 * @return Money
	 */
	public static function usd($units)
	{
		return new Money($units, new Usd);
	}

	private function isSameCurrency(Money $other)
	{
		return $this->currency->equals($other->currency);
	}

	/**
	 * @throws Verraes\Money\InvalidArgumentException
	 */
	private function assertSameCurrency(Money $other)
	{
		if(!$this->isSameCurrency($other)) {
			throw new InvalidArgumentException('Different currencies');
		}
	}

	public function equals(Money $other)
	{
		return
			$this->isSameCurrency($other)
			&& $this->units == $other->units;
	}

	public function compare(Money $other)
	{
		$this->assertSameCurrency($other);
		if($this->units < $other->units) {
			return -1;
		} elseif($this->units == $other->units) {
			return 0;
		} else {
			return 1;
		}
	}

	public function greaterThan(Money $other)
	{
		return 1 == $this->compare($other);
	}

	public function lessThan(Money $other)
	{
		return -1 == $this->compare($other);
	}

	/**
	 * @return int
	 */
	public function getUnits()
	{
		return $this->units;
	}

	/**
	 * @return Verraes\Money\Currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	public function add(Money $other)
	{
		$this->assertSameCurrency($other);
		return new self($this->units + $other->units, $this->currency);
	}

	public function subtract(Money $other)
	{
		$this->assertSameCurrency($other);
		return new self($this->units - $other->units, $this->currency);
	}

	/**
	 * @throws Verraes\Money\InvalidArgumentException
	 */
	private function assertOperand($operand)
	{
		if(!is_int($operand) && !is_float($operand)) {
			throw new InvalidArgumentException('Operand should be an integer or a float');
		}
	}

	/**
	 * @throws Verraes\Money\InvalidArgumentException
	 */
	private function assertRoundingMode($rounding_mode)
	{
		if(!in_array($rounding_mode, array(self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP))) {
			throw new InvalidArgumentException('Operand should be an integer or a float');
		}
	}

	public function multiply($operand, $rounding_mode = self::ROUND_HALF_UP)
	{
		$this->assertOperand($operand);
		$this->assertRoundingMode($rounding_mode);

		$result = (int) round($this->units * $operand, 0, $rounding_mode);
		return new Money($result, $this->currency);
	}

	public function divide($operand, $rounding_mode = self::ROUND_HALF_UP)
	{
		$this->assertOperand($operand);
		$this->assertRoundingMode($rounding_mode);

		$result = (int) round($this->units / $operand, 0, $rounding_mode);
		return new Money($result, $this->currency);
	}

	/**
	 * Allocate the money according to a list of ratio's
	 * @param array $ratios List of ratio's
	 */
	public function allocate(array $ratios)
	{
		$remainder = $this->units;
		$results = array();
		$total = array_sum($ratios);

		foreach($ratios as $ratio)
		{
			$share = (int) floor($this->units * $ratio / $total);
			$results[] = new Money($share, $this->currency);
			$remainder -= $share;
		}
		for($i = 0; $remainder > 0; $i++)
		{
			$results[$i]->units++;
			$remainder--;
		}
		return $results;
	}


}