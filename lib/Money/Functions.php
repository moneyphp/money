<?php
/**
 * This file is part of the Money library
*
* Copyright (c) 2011 Mathias Verraes
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use Money\Money;
use Money\Currency;

/**
 * Create a USD Money instance
 * @return \Money\Money
 */
function €($amount)
{
	return new Money($amount, new Currency('EUR'));
};

/**
 * Create a USD Money instance
 * @return \Money\Money
 */
function §($amount)
{
	return new Money($amount, new Currency('USD'));
};

/**
 * Create a JPY Money instance
 * @return \Money\Money
 */
function ¥($amount)
{
	return new Money($amount, new Currency('JPY'));
};

/**
 * Create a GBP Money instance
 * @return \Money\Money
 */
function £($amount)
{
	return new Money($amount, new Currency('GBP'));
};


