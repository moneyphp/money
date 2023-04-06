<?php

declare(strict_types=1);

namespace Benchmark\Money\Calculator;

use Money\Calculator\PerformanceOptimizedDelegateCalculator;

final class PerformanceOptimizedDelegateCalculatorBench extends CalculatorBench
{
    protected function getCalculator(): string
    {
        return PerformanceOptimizedDelegateCalculator::class;
    }
}
