<?php

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;

final class BcMathCalculatorTest extends CalculatorTest
{
    protected function getCalculator()
    {
        return new BcMathCalculator();
    }

    public function testAddSubtractWhenScaleSet()
    {
        $calculator = $this->getCalculator();
        $defaultScale = ini_get('bcmath.scale');

        bcscale(1);
        $this->assertSame('2', $calculator->add(1, 1));
        $this->assertSame('1', $calculator->subtract(2, 1));
        bcscale($defaultScale);
    }
}
