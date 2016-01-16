<?php

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;

final class BcMathCalculatorTest extends CalculatorTest
{
    protected function getCalculator()
    {
        return new BcMathCalculator();
    }
}
