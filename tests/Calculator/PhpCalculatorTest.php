<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

class PhpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator(): PhpCalculator
    {
        return new PhpCalculator();
    }
}
