<?php

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

/**
 * @requires extension gmp
 */
class GmpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new GmpCalculator();
    }

    /**
     * @test
     */
    public function it_multiplies_zero()
    {
        $this->assertSame('0', $this->getCalculator()->multiply('0', '0.8'));
    }

    /**
     * @test
     */
    public function it_floors_zero()
    {
        $this->assertSame('0', $this->getCalculator()->floor('0'));
    }

    /**
     * @test
     */
    public function it_compares_zero_with_fraction()
    {
        $this->assertSame(1, $this->getCalculator()->compare('0.5', '0'));
    }
}
