<?php

declare(strict_types=1);

namespace Benchmark\Money\Calculator;

use Money\Calculator\GmpCalculator;

class GmpCalculatorBench extends CalculatorBench
{
    protected function getCalculator(): string
    {
        return GmpCalculator::class;
    }
}
