<?php

namespace Tests\Money\Calculator;

use Money\Calculator\BcMathCalculator;

/**
 * @requires extension bcmath
 */
final class BcMathCalculatorTest extends CalculatorTestCase
{
    private $defaultScale;

    protected function getCalculator()
    {
        return new BcMathCalculator();
    }

    public function setUp()
    {
        $this->defaultScale = ini_get('bcmath.scale');
    }

    public function tearDown()
    {
        bcscale($this->defaultScale);
    }

    /**
     * @dataProvider additionExamples
     * @test
     */
    public function it_adds_two_values_with_scale_set($value1, $value2, $expected)
    {
        bcscale(1);

        $this->assertEquals($expected, $this->getCalculator()->add($value1, $value2));
    }

    /**
     * @dataProvider subtractionExamples
     * @test
     */
    public function it_subtracts_a_value_from_another_with_scale_set($value1, $value2, $expected)
    {
        bcscale(1);

        $this->assertEquals($expected, $this->getCalculator()->subtract($value1, $value2));
    }
}
