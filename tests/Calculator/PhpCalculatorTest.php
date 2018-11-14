<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

class PhpCalculatorTest extends CalculatorTestCase
{
    protected function supported()
    {
        return true;
    }

    protected function getCalculator()
    {
        return new PhpCalculator();
    }
}
