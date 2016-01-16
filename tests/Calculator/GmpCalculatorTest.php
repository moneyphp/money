<?php

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

final class GmpCalculatorTest extends CalculatorTest
{
    protected function getCalculator()
    {
        return new GmpCalculator();
    }
}
