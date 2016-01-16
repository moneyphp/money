<?php

namespace Money\Tests;

use Money\Calculator\BcMathCalculator;
use Money\Money;

class BcMathCalculatorTest extends \PHPUnit_Framework_TestCase
{

    public function testCompare()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals(1, $calculator->compare(2, 1));
        $this->assertEquals(-1, $calculator->compare(1, 2));
        $this->assertEquals(0, $calculator->compare(1, 1));
    }

    public function testAdd()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals(2, $calculator->add(1, 1));
    }

    public function testSubtract()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals(1, $calculator->subtract(2, 1));
    }

    public function testMultiply()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('1.5', $calculator->multiply(1, 1.5));
        $this->assertEquals('12.50', $calculator->multiply(10, 1.2500));
    }

    public function testDivide()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('1.5', $calculator->divide(3, 2));
        $this->assertEquals('2.5', $calculator->divide(10, 4));
    }

    public function testCeil()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('2', $calculator->ceil(1.2));
    }

    public function testFloor()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('2', $calculator->floor(2.7));
    }

    public function testRound()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('3', $calculator->round(2.6, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('4', $calculator->round(3.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2.1, Money::ROUND_HALF_ODD));
        $this->assertEquals('3', $calculator->round(2.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('3', $calculator->round(3.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('2', $calculator->round(2.5, Money::ROUND_HALF_DOWN));
        $this->assertEquals('3', $calculator->round(2.6, Money::ROUND_HALF_DOWN));
        $this->assertEquals('2', $calculator->round(2.2, Money::ROUND_HALF_UP));
        $this->assertEquals('3', $calculator->round(2.5, Money::ROUND_HALF_UP));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_UP));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_DOWN));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_ODD));
        $this->assertEquals('12', $calculator->round('12.50', Money::ROUND_HALF_DOWN));
    }

    public function testShare()
    {
        $calculator = new BcMathCalculator();
        $this->assertEquals('5', $calculator->share(10, 2, 4));
    }

}
