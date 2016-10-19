<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

final class PhpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new PhpCalculator();
    }
}
