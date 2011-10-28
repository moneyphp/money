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

class Currency
{
	/** @var string */
	private $name;

	const EUR = 'EUR';
	const USD = 'USD';
	const GBP = 'GBP';
	const JPY = 'JPY';

	public function __construct($name)
	{
		if(!defined("self::$name")) {
			throw new UnknownCurrencyException($name);
		}
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return  bool
	 */
	public function equals(Currency $other)
	{
		return $this->name === $other->name;
	}
}