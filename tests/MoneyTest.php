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
use UnexpectedValueException;

/**
 * @coversDefaultClass Money\Money
 * @uses Money\Currency
 * @uses Money\Money
 * @uses Money\CurrencyPair
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__callStatic
     */
    public function testFactoryMethods()
    {
        $this->assertEquals(
            Money::EUR(25),
            Money::EUR(10)->add(Money::EUR(15))
        );
        $this->assertEquals(
            Money::USD(25),
            Money::USD(10)->add(Money::USD(15))
        );
    }

    /**
     * @covers ::getAmount
     * @covers ::getCurrency
     */
    public function testGetters()
    {
        $m = new Money(100, $euro = new Currency('EUR'));
        $this->assertEquals(100, $m->getAmount());
        $this->assertEquals($euro, $m->getCurrency());
    }

    /**
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     */
    public function testDecimalsThrowException()
    {
        $money = new Money(0.01, new Currency('EUR'));
    }

    /**
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     */
    public function testStringThrowsException()
    {
        $money = new Money('100', new Currency('EUR'));
    }

    /**
     * @covers ::equals
     */
    public function testEquality()
    {
        $m1 = new Money(100, new Currency('EUR'));
        $m2 = new Money(100, new Currency('EUR'));
        $m3 = new Money(100, new Currency('USD'));
        $m4 = new Money(50, new Currency('EUR'));

        $this->assertTrue($m1->equals($m2));
        $this->assertFalse($m1->equals($m3));
        $this->assertFalse($m1->equals($m4));
    }

    public function testAddition()
    {
        $m1 = new Money(100, new Currency('EUR'));
        $m2 = new Money(100, new Currency('EUR'));
        $sum = $m1->add($m2);
        $expected = new Money(200, new Currency('EUR'));

        $this->assertEquals($expected, $sum);

        // Should return a new instance
        $this->assertNotSame($sum, $m1);
        $this->assertNotSame($sum, $m2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDifferentCurrenciesCannotBeAdded()
    {
        $m1 = new Money(100, new Currency('EUR'));
        $m2 = new Money(100, new Currency('USD'));
        $m1->add($m2);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testResultNotAnInteger()
    {
        $m1 = new Money(PHP_INT_MAX, new Currency('EUR'));
        $m2 = new Money(1, new Currency('EUR'));
        $m1->add($m2);
    }

    /**
     * @covers ::subtract
     */
    public function testSubtraction()
    {
        $m1 = new Money(100, new Currency('EUR'));
        $m2 = new Money(200, new Currency('EUR'));
        $diff = $m1->subtract($m2);
        $expected = new Money(-100, new Currency('EUR'));

        $this->assertEquals($expected, $diff);

        // Should return a new instance
        $this->assertNotSame($diff, $m1);
        $this->assertNotSame($diff, $m2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDifferentCurrenciesCannotBeSubtracted()
    {
        $m1 = new Money(100, new Currency('EUR'));
        $m2 = new Money(100, new Currency('USD'));
        $m1->subtract($m2);
    }

    public function testMultiplication()
    {
        $m = new Money(1, new Currency('EUR'));
        $this->assertEquals(
            new Money(2, new Currency('EUR')),
            $m->multiply(1.5)
        );
        $this->assertEquals(
            new Money(1, new Currency('EUR')),
            $m->multiply(1.5, Money::ROUND_HALF_DOWN)
        );

        $this->assertNotSame($m, $m->multiply(2));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMultiplicationOperand()
    {
        $m = new Money(1, new Currency('EUR'));
        $m->multiply('operand');
    }

    /**
     * @covers ::assertRoundingMode
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRoundingMode()
    {
        $m = new Money(1, new Currency('EUR'));
        $m->multiply(1.2345, 'ROUNDING_MODE');
    }

    /**
     * @expectedException OverflowException
     */
    public function testMultiplicationOverflow()
    {
        $m = new Money(PHP_INT_MAX, new Currency('EUR'));
        $m->multiply(2);
    }

    /**
     * @expectedException UnderflowException
     */
    public function testMultiplicationUnderflow()
    {
        $m = new Money(~PHP_INT_MAX, new Currency('EUR'));
        $m->multiply(2);
    }

    /**
     * @covers ::divide
     */
    public function testDivision()
    {
        $m = new Money(10, new Currency('EUR'));
        $this->assertEquals(
            new Money(3, new Currency('EUR')),
            $m->divide(3)
        );
        $this->assertEquals(
            new Money(2, new Currency('EUR')),
            $m->divide(4, Money::ROUND_HALF_EVEN)
        );
        $this->assertEquals(
            new Money(3, new Currency('EUR')),
            $m->divide(3, Money::ROUND_HALF_ODD)
        );

        $this->assertNotSame($m, $m->divide(2));
    }

    public function testComparison()
    {
        $euro1 = new Money(1, new Currency('EUR'));
        $euro2 = new Money(2, new Currency('EUR'));
        $usd = new Money(1, new Currency('USD'));

        $this->assertTrue($euro2->greaterThan($euro1));
        $this->assertFalse($euro1->greaterThan($euro2));
        $this->assertTrue($euro1->lessThan($euro2));
        $this->assertFalse($euro2->lessThan($euro1));

        $this->assertEquals(-1, $euro1->compare($euro2));
        $this->assertEquals(1, $euro2->compare($euro1));
        $this->assertEquals(0, $euro1->compare($euro1));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDifferentCurrenciesCannotBeCompared()
    {
        Money::EUR(1)->compare(Money::USD(1));
    }

    /**
     * @covers ::allocate
     */
    public function testAllocation()
    {
        $m = new Money(100, new Currency('EUR'));
        list($part1, $part2, $part3) = $m->allocate(array(1, 1, 1));
        $this->assertEquals(new Money(34, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(33, new Currency('EUR')), $part2);
        $this->assertEquals(new Money(33, new Currency('EUR')), $part3);

        $m = new Money(101, new Currency('EUR'));
        list($part1, $part2, $part3) = $m->allocate(array(1, 1, 1));
        $this->assertEquals(new Money(34, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(34, new Currency('EUR')), $part2);
        $this->assertEquals(new Money(33, new Currency('EUR')), $part3);
    }

    /**
     * @covers ::allocate
     */
    public function testAllocationOrderIsImportant()
    {
        $m = new Money(5, new Currency('EUR'));
        list($part1, $part2) = $m->allocate(array(3, 7));
        $this->assertEquals(new Money(2, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(3, new Currency('EUR')), $part2);

        $m = new Money(5, new Currency('EUR'));
        list($part1, $part2) = $m->allocate(array(7, 3));
        $this->assertEquals(new Money(4, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(1, new Currency('EUR')), $part2);
    }

    /**
     * @covers ::allocateTo
     */
    public function testAllocationTo($value='')
    {
        $m = new Money(15, new Currency('EUR'));
        list($part1, $part2) = $m->allocateTo(2);
        $this->assertEquals(new Money(8, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(7, new Currency('EUR')), $part2);

        $m = new Money(10, new Currency('EUR'));
        list($part1, $part2) = $m->allocateTo(2);
        $this->assertEquals(new Money(5, new Currency('EUR')), $part1);
        $this->assertEquals(new Money(5, new Currency('EUR')), $part2);
    }

    /**
     * @covers ::allocateTo
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Number of targets must be an integer
     */
    public function testAllocationToInvalidTargets()
    {
        $m = new Money(15, new Currency('EUR'));
        $m->allocateTo('target');
    }

    /**
     * @covers ::isZero
     * @covers ::isNegative
     * @covers ::isPositive
     */
    public function testComparators()
    {
        $this->assertTrue(Money::EUR(0)->isZero());
        $this->assertTrue(Money::EUR(-1)->isNegative());
        $this->assertTrue(Money::EUR(1)->isPositive());
        $this->assertFalse(Money::EUR(1)->isZero());
        $this->assertFalse(Money::EUR(1)->isNegative());
        $this->assertFalse(Money::EUR(-1)->isPositive());
    }

    public static function provideStrings()
    {
        return array(
            array("1000", 100000),
            array("1000.0", 100000),
            array("1000.00", 100000),
            array("0.01", 1),
            array("1", 100),
            array("-1000", -100000),
            array("-1000.0", -100000),
            array("-1000.00", -100000),
            array("-0.01", -1),
            array("-1", -100),
            array("+1000", 100000),
            array("+1000.0", 100000),
            array("+1000.00", 100000),
            array("+0.01", 1),
            array("+1", 100),
            array(".99", 99),
            array("-.99", -99),
        );
    }

    /**
     * @covers ::stringToUnits
     * @dataProvider provideStrings
     */
    public function testStringToUnits($string, $units)
    {
        $this->assertEquals($units, Money::stringToUnits($string));
    }

    /**
     * @covers ::stringToUnits
     * @expectedException InvalidArgumentException
     */
    public function testCannotConvertStringToUnits()
    {
        Money::stringToUnits('THIS_IS_NOT_CONVERTABLE_TO_UNIT');
    }
}
