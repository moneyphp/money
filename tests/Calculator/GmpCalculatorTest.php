<?php

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

/**
 * @requires extension gmp
 */
final class GmpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new GmpCalculator();
    }
}
