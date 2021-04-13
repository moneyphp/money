<?php

namespace Tests\Money\Calculator;

use function ini_get;
use Money\Calculator\BcMathCalculator;

/**
 * @requires extension bcmath
 */
class BcMathCalculatorTest extends CalculatorTestCase
{
    private $defaultScale;

    protected function getCalculator()
    {
        return new BcMathCalculator();
    }

    public function setUp(): void
    {
        $this->defaultScale = ini_get('bcmath.scale');
    }

    public function tearDown(): void
    {
        bcscale($this->defaultScale);
    }

    /**
     * @dataProvider additionExamples
     * @test
     */
    public function itAddsTwoValuesWithScaleSet($value1, $value2, $expected)
    {
        bcscale(1);

        $this->assertEquals($expected, $this->getCalculator()->add($value1, $value2));
    }

    /**
     * @dataProvider subtractionExamples
     * @test
     */
    public function itSubtractsAValueFromAnotherWithScaleSet($value1, $value2, $expected)
    {
        bcscale(1);

        $this->assertEquals($expected, $this->getCalculator()->subtract($value1, $value2));
    }

    /**
     * @test
     */
    public function itComparesNumbersCloseToZero()
    {
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.0005'));
        $this->assertEquals(1, $this->getCalculator()->compare('1', '0.000000000000000000000000005'));
    }

    /**
     * @test
     */
    public function itUsesScaleForAdd()
    {
        $this->assertEquals('0.00130154000000', $this->getCalculator()->add('0.00125148', '0.00005006'));
    }

    /**
     * @test
     */
    public function itUsesScaleForSubtract()
    {
        $this->assertEquals('0.00120142', $this->getCalculator()->subtract('0.00125148', '0.00005006'));
    }
}
