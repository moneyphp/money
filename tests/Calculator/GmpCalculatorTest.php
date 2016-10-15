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

    /**
     * @dataProvider divisionExamples
     * @test
     */
    public function it_divides_a_value_by_another($value1, $value2, $expected)
    {
        $this->markTestSkipped('To be fixed soon');
    }
}
