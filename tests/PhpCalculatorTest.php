<?php
namespace Money\Tests;

use Money\Calculator\PhpCalculator;
use Money\Money;

class PhpCalculatorTest extends \PHPUnit_Framework_TestCase
{

    public function testCompare()
    {
        $calculator = new PhpCalculator();
        $this->assertSame(1, $calculator->compare(2, 1));
        $this->assertSame(-1, $calculator->compare(1, 2));
        $this->assertSame(0, $calculator->compare(1, 1));
    }

    public function testAdd()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('2', $calculator->add(1, 1));
    }

    public function testSubtract()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('1', $calculator->subtract(2, 1));
    }

    public function testMultiply()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('1.5', $calculator->multiply(1, 1.5));
    }

    public function testDivide()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('1.5', $calculator->divide(3, 2));
    }

    public function testCeil()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('2', $calculator->ceil(1.2));
    }

    public function testFloor()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('2', $calculator->floor(2.7));
    }

    public function testRound()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('3', $calculator->round(2.6, Money::ROUND_HALF_EVEN));
        $this->assertSame('2', $calculator->round(2.5, Money::ROUND_HALF_EVEN));
        $this->assertSame('2', $calculator->round(2.1, Money::ROUND_HALF_ODD));
        $this->assertSame('3', $calculator->round(2.5, Money::ROUND_HALF_ODD));
        $this->assertSame('2', $calculator->round(2.5, Money::ROUND_HALF_DOWN));
        $this->assertSame('3', $calculator->round(2.6, Money::ROUND_HALF_DOWN));
        $this->assertSame('2', $calculator->round(2.2, Money::ROUND_HALF_UP));
        $this->assertSame('3', $calculator->round(2.5, Money::ROUND_HALF_UP));
        $this->assertSame('2', $calculator->round(2, Money::ROUND_HALF_UP));
        $this->assertSame('2', $calculator->round(2, Money::ROUND_HALF_DOWN));
        $this->assertSame('2', $calculator->round(2, Money::ROUND_HALF_EVEN));
        $this->assertSame('2', $calculator->round(2, Money::ROUND_HALF_ODD));
    }

    public function testShare()
    {
        $calculator = new PhpCalculator();
        $this->assertSame('5', $calculator->share(10, 2, 4));
    }

    /**
     * @expectedException \OverflowException
     */
    public function testMultiplicationOverflow()
    {
        $calculator = new PhpCalculator();
        $calculator->multiply(PHP_INT_MAX, 2);
    }

    /**
     * @expectedException \UnderflowException
     */
    public function testMultiplicationUnderflow()
    {
        $calculator = new PhpCalculator();
        $calculator->multiply(~PHP_INT_MAX, 2);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testResultNotAnInteger()
    {
        $calculator = new PhpCalculator();
        $calculator->add(PHP_INT_MAX, 1);
    }

}
