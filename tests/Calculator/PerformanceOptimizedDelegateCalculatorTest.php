<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\PerformanceOptimizedDelegateCalculator;

/**
 * @requires extension bcmath
 * @requires extension gmp
 * @covers \Money\Calculator\PerformanceOptimizedDelegateCalculator
 */
class PerformanceOptimizedDelegateCalculatorTest extends CalculatorTestCase
{
    /**
     * @return PerformanceOptimizedDelegateCalculator
     * @psalm-return class-string<PerformanceOptimizedDelegateCalculator>
     */
    protected function getCalculator(): string
    {
        return PerformanceOptimizedDelegateCalculator::class;
    }
}
