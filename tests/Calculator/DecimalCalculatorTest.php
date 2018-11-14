<?php

namespace Tests\Money\Calculator;

use Money\Calculator\DecimalCalculator;

/**
 * @requires extension bcmath
 */
class DecimalCalculatorTest extends CalculatorTestCase
{
    protected function supported()
    {
        return DecimalCalculator::supported();
    }

    protected function getCalculator()
    {
        return new DecimalCalculator();
    }
}
