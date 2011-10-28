<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'bootstrap.php';
require_once __DIR__.'/../lib/Money/Functions.php';

use Money\Money;
use Money\Currency;

class FunctionsTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	public function CreatesInstances()
	{
		$this->assertEquals(Money::EUR(500), €(500));
		$this->assertEquals(Money::USD(500), §(500));
		$this->assertEquals(Money::JPY(500), ¥(500));
		$this->assertEquals(Money::GBP(500), £(500));
	}
}