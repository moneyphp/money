<?php
/**
 * This file is part of the Verraes\Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'bootstrap.php';

use Verraes\Money\Money;
use Verraes\Money\Currency;
use Verraes\Money\Usd;
use Verraes\Money\Euro;

class MoneyTest extends PHPUnit_Framework_TestCase
{

	private function assertMoneyEquals(Money $expected, Money $actual, $message = null)
	{
		$str = sprintf(
			"Failed asserting that <Money:%s %s> matches expected <Money:%s %s>",
			$actual->getCurrency()->getName(), $actual->getUnits(),
			$expected->getCurrency()->getName(), $expected->getUnits()
		);

		return $this->assertTrue(
			$actual->equals($expected),
			($message ? $message.PHP_EOL: '') . $str
		);
	}

	public function testFactoryMethods()
	{
		$this->assertMoneyEquals(
			Money::euro(25),
			Money::euro(10)->add(Money::euro(15))
		);
		$this->assertMoneyEquals(
			Money::usd(25),
			Money::usd(10)->add(Money::usd(15))
		);
	}

	public function testGetters()
	{
		$m = new Money(100, $euro = new Euro);
		$this->assertEquals(100, $m->getUnits());
		$this->assertEquals($euro, $m->getCurrency());
	}

	/**
	 * @expectedException Verraes\Money\InvalidArgumentException
	 */
	public function testDecimalsThrowException()
	{
		$money = new Money(0.01, new Euro);
	}

	/**
	 * @expectedException Verraes\Money\InvalidArgumentException
	 */
	public function testStringThrowsException()
	{
		$money = new Money('100', new Euro);
	}

	public function testEquality()
	{
		$m1 = new Money(100, new Euro);
		$m2 = new Money(100, new Euro);
		$m3 = new Money(100, new Usd);
		$m4 = new Money(50, new Euro);

		$this->assertTrue($m1->equals($m2));
		$this->assertFalse($m1->equals($m3));
		$this->assertFalse($m1->equals($m4));
	}

	public function testAddition()
	{
		$m1 = new Money(100, new Euro);
		$m2 = new Money(100, new Euro);
		$sum = $m1->add($m2);
		$expected = new Money(200, new Euro);

		$this->assertMoneyEquals($expected, $sum);

		// Should return a new instance
		$this->assertNotSame($sum, $m1);
		$this->assertNotSame($sum, $m2);
	}

	/**
	 * @expectedException Verraes\Money\InvalidArgumentException
	 */
	public function testDifferentCurrenciesCannotBeAdded()
	{
		$m1 = new Money(100, new Euro);
		$m2 = new Money(100, new Usd);
		$m1->add($m2);
	}

	public function testSubtraction()
	{
		$m1 = new Money(100, new Euro);
		$m2 = new Money(200, new Euro);
		$diff = $m1->subtract($m2);
		$expected = new Money(-100, new Euro);

		$this->assertMoneyEquals($expected, $diff);

		// Should return a new instance
		$this->assertNotSame($diff, $m1);
		$this->assertNotSame($diff, $m2);
	}

	/**
	 * @expectedException Verraes\Money\InvalidArgumentException
	 */
	public function testDifferentCurrenciesCannotBeSubtracted()
	{
		$m1 = new Money(100, new Euro);
		$m2 = new Money(100, new Usd);
		$m1->subtract($m2);
	}

	public function testMultiplication()
	{
		$m = new Money(1, new Euro);
		$this->assertMoneyEquals(
			new Money(2, new Euro),
			$m->multiply(1.5)
		);
		$this->assertMoneyEquals(
			new Money(1, new Euro),
			$m->multiply(1.5, Money::ROUND_HALF_DOWN)
		);

		$this->assertNotSame($m, $m->multiply(2));
	}

	public function testDivision()
	{
		$m = new Money(10, new Euro);
		$this->assertMoneyEquals(
			new Money(3, new Euro),
			$m->divide(3)
		);
		$this->assertMoneyEquals(
			new Money(2, new Euro),
			$m->divide(4, Money::ROUND_HALF_EVEN)
		);
		$this->assertMoneyEquals(
			new Money(3, new Euro),
			$m->divide(3, Money::ROUND_HALF_ODD)
		);

		$this->assertNotSame($m, $m->divide(2));
	}

	public function testComparison()
	{
		$euro1 = new Money(1, new Euro);
		$euro2 = new Money(2, new Euro);
		$usd = new Money(1, new Usd);

		$this->assertTrue($euro2->greaterThan($euro1));
		$this->assertFalse($euro1->greaterThan($euro2));
		$this->assertTrue($euro1->lessThan($euro2));
		$this->assertFalse($euro2->lessThan($euro1));

		$this->assertEquals(-1, $euro1->compare($euro2));
		$this->assertEquals(1, $euro2->compare($euro1));
		$this->assertEquals(0, $euro1->compare($euro1));
	}

	/**
	 * @expectedException Verraes\Money\InvalidArgumentException
	 */
	public function testDifferentCurrenciesCannotBeCompared()
	{
		Money::euro(1)->compare(Money::usd(1));
	}

	public function testAllocation()
	{
		$m = new Money(100, new Euro);
		list($part1, $part2, $part3) = $m->allocate(array(1, 1, 1));
		$this->assertMoneyEquals(new Money(34, new Euro), $part1);
		$this->assertMoneyEquals(new Money(33, new Euro), $part2);
		$this->assertMoneyEquals(new Money(33, new Euro), $part3);

		$m = new Money(101, new Euro);
		list($part1, $part2, $part3) = $m->allocate(array(1, 1, 1));
		$this->assertMoneyEquals(new Money(34, new Euro), $part1);
		$this->assertMoneyEquals(new Money(34, new Euro), $part2);
		$this->assertMoneyEquals(new Money(33, new Euro), $part3);

		$m = new Money(5, new Euro);
		list($part1, $part2) = $m->allocate(array(3, 7));
		$this->assertMoneyEquals(new Money(2, new Euro), $part1);
		$this->assertMoneyEquals(new Money(3, new Euro), $part2);
	}
}