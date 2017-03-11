<?php

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

/**
 * @requires extension gmp
 */
final class GmpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new GmpCalculator();
    }

    public function testMultiplyZero()
    {
        $this->assertSame('0', $this->getCalculator()->multiply('0', '0.8'));
    }

    public function testFloorZero()
    {
        $this->assertSame('0', $this->getCalculator()->floor('0'));
    }
}
