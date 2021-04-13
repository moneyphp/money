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
    public function itMultipliesZero()
    {
        $this->assertSame('0', $this->getCalculator()->multiply('0', '0.8'));
    }

    /**
     * @test
     */
    public function itFloorsZero()
    {
        $this->assertSame('0', $this->getCalculator()->floor('0'));
    }

    /**
     * @test
     */
    public function itComparesZeroWithFraction()
    {
        $this->assertSame(1, $this->getCalculator()->compare('0.5', '0'));
    }
}
