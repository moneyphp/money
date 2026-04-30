<?php

declare(strict_types=1);

namespace Benchmark\Money\Calculator;

use Money\Calculator\BcMathCalculator;

class BcMathCalculatorBench extends CalculatorBench
{
    protected function getCalculator(): string
    {
        return BcMathCalculator::class;
    }
}
