<?php
namespace Money\Tests;

use Money\Calculator\GmpCalculator;
use Money\Money;

class GmpCalculatorTest extends \PHPUnit_Framework_TestCase
{

    public function testCompare()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals(1, $calculator->compare(2, 1));
        $this->assertEquals(-1, $calculator->compare(1, 2));
        $this->assertEquals(0, $calculator->compare(1, 1));
    }

    public function testAdd()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals(2, $calculator->add(1, 1));
    }

    public function testSubtract()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals(1, $calculator->subtract(2, 1));
    }

    public function testMultiply()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals('2', $calculator->multiply(1, 2));
        $this->assertEquals('1.5', $calculator->multiply(1, 1.5));
    }

    public function testDivide()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals('2', $calculator->divide(4, 2));
        $this->assertEquals('1.5', $calculator->divide(3, 2));
        $this->assertEquals('5', $calculator->divide(20, 4));
    }

    public function testCeil()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals('2', $calculator->ceil(1.2));
    }

    public function testFloor()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals('2', $calculator->floor(2.7));
    }

    public function testRound()
    {
        $calculator = new GmpCalculator();
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
    }

    public function testShare()
    {
        $calculator = new GmpCalculator();
        $this->assertEquals('5', $calculator->share(10, 2, 4));
    }

}
