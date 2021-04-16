<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

/** @covers \Money\Calculator\PhpCalculator */
class PhpCalculatorTest extends CalculatorTestCase
{
    /**
     * @return PhpCalculator
     * @psalm-return class-string<PhpCalculator>
     */
    protected function getCalculator(): string
    {
        return PhpCalculator::class;
    }
}
