<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

class PhpCalculatorTest extends CalculatorTest
{
    protected function getCalculator()
    {
        return new PhpCalculator();
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
