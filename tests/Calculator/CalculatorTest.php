<?php

namespace Tests\Money\Calculator;

use Money\Calculator;
use Money\Money;

abstract class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Calculator
     */
    abstract protected function getCalculator();

    public function testCompare()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals(1, $calculator->compare(2, 1));
        $this->assertEquals(-1, $calculator->compare(1, 2));
        $this->assertEquals(0, $calculator->compare(1, 1));
    }

    public function testAdd()
    {
        $calculator = $this->getCalculator();

        $this->assertSame('2', $calculator->add(1, 1));
    }

    public function testSubtract()
    {
        $calculator = $this->getCalculator();

        $this->assertSame('1', $calculator->subtract(2, 1));
    }

    public function testMultiply()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('1.5', $calculator->multiply(1, 1.5));
        $this->assertEquals('12.50', $calculator->multiply(10, 1.2500));
    }

    public function testDivide()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('1.5', $calculator->divide(3, 2));
        $this->assertEquals('2.5', $calculator->divide(10, 4));
    }

    public function testCeil()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('2', $calculator->ceil(1.2));
        $this->assertEquals('-1', $calculator->ceil(-1.2));
    }

    public function testFloor()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('2', $calculator->floor(2.7));
        $this->assertEquals('-3', $calculator->floor(-2.7));
    }

    public function testRoundHalfOddEvent()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('3', $calculator->round(2.6, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('4', $calculator->round(3.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('-3', $calculator->round(-2.6, Money::ROUND_HALF_EVEN));
        $this->assertEquals('-2', $calculator->round(-2.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('-4', $calculator->round(-3.5, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2.1, Money::ROUND_HALF_ODD));
        $this->assertEquals('3', $calculator->round(2.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('3', $calculator->round(3.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('-2', $calculator->round(-2.1, Money::ROUND_HALF_ODD));
        $this->assertEquals('-3', $calculator->round(-2.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('-3', $calculator->round(-3.5, Money::ROUND_HALF_ODD));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_EVEN));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_ODD));
        $this->assertEquals('-2', $calculator->round(-2, Money::ROUND_HALF_ODD));
    }

    public function testRoundHalfUpDown()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('2', $calculator->round(2.5, Money::ROUND_HALF_DOWN));
        $this->assertEquals('3', $calculator->round(2.6, Money::ROUND_HALF_DOWN));
        $this->assertEquals('-2', $calculator->round(-2.5, Money::ROUND_HALF_DOWN));
        $this->assertEquals('-3', $calculator->round(-2.6, Money::ROUND_HALF_DOWN));
        $this->assertEquals('2', $calculator->round(2.2, Money::ROUND_HALF_UP));
        $this->assertEquals('3', $calculator->round(2.5, Money::ROUND_HALF_UP));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_UP));
        $this->assertEquals('-3', $calculator->round(-2.5, Money::ROUND_HALF_UP));
        $this->assertEquals('-2', $calculator->round(-2, Money::ROUND_HALF_UP));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_DOWN));
        $this->assertEquals('12', $calculator->round('12.50', Money::ROUND_HALF_DOWN));
        $this->assertEquals('-12', $calculator->round('-12.50', Money::ROUND_HALF_DOWN));
        $this->assertEquals('-2', $calculator->round(-1.5, Money::ROUND_HALF_UP));
        $this->assertEquals('-8329', $calculator->round(-8328.578947368, Money::ROUND_HALF_UP));
        $this->assertEquals('-8329', $calculator->round(-8328.5, Money::ROUND_HALF_UP));
        $this->assertEquals('-8328', $calculator->round(-8328.5, Money::ROUND_HALF_DOWN));
    }

    public function testRoundHalfInfinity()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('3', $calculator->round(2.5, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('3', $calculator->round(2.6, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('-2', $calculator->round(-2.5, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('-3', $calculator->round(-2.6, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('13', $calculator->round('12.50', Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('-12', $calculator->round('-12.50', Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('-8328', $calculator->round(-8328.5, Money::ROUND_HALF_POSITIVE_INFINITY));
        $this->assertEquals('2', $calculator->round(2.2, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('2', $calculator->round(2.5, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('2', $calculator->round(2, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('-3', $calculator->round(-2.5, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('-2', $calculator->round(-2, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('-2', $calculator->round(-1.5, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('-8329', $calculator->round(-8328.578947368, Money::ROUND_HALF_NEGATIVE_INFINITY));
        $this->assertEquals('-8329', $calculator->round(-8328.5, Money::ROUND_HALF_NEGATIVE_INFINITY));
    }

    public function testShare()
    {
        $calculator = $this->getCalculator();

        $this->assertEquals('5', $calculator->share(10, 2, 4));
    }
}
