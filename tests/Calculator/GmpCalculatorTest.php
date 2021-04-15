<?php

declare(strict_types=1);

namespace Tests\Money\Calculator;

use Money\Calculator\GmpCalculator;

/**
 * @requires extension gmp
 */
class GmpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator(): GmpCalculator
    {
        return new GmpCalculator();
    }

    /**
     * @test
     */
    public function itMultipliesZero(): void
    {
        $this->assertSame('0', $this->getCalculator()->multiply('0', '0.8'));
    }

    /**
     * @test
     */
    public function itFloorsZero(): void
    {
        $this->assertSame('0', $this->getCalculator()->floor('0'));
    }

    /**
     * @test
     */
    public function itComparesZeroWithFraction(): void
    {
        $this->assertSame(1, $this->getCalculator()->compare('0.5', '0'));
    }

    /**
     * @test
     */
    public function it_divides_bug538(): void
    {
        $this->assertSame('-4.54545454545455', $this->getCalculator()->divide('-500', '110'));
    }
}
