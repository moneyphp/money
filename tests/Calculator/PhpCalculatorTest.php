<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

class PhpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new PhpCalculator();
    }
}
