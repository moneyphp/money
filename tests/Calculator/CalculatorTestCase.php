<?php

namespace Tests\Money\Calculator;

use Money\Calculator;
use Tests\Money\RoundExamples;

abstract class CalculatorTestCase extends \PHPUnit_Framework_TestCase
{
    use RoundExamples;

    /**
     * @return Calculator
     */
    abstract protected function getCalculator();

    /**
     * @dataProvider additionExamples
     * @test
     */
    public function it_adds_two_values($value1, $value2, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->add($value1, $value2));
    }

    /**
     * @dataProvider subtractionExamples
     * @test
     */
    public function it_subtracts_a_value_from_another($value1, $value2, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->subtract($value1, $value2));
    }

    /**
     * @dataProvider multiplicationExamples
     * @test
     */
    public function it_multiplies_a_value_by_another($value1, $value2, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->multiply($value1, $value2));
    }

    /**
     * @dataProvider divisionExamples
     * @test
     */
    public function it_divides_a_value_by_another($value1, $value2, $expected)
    {
        $result = $this->getCalculator()->divide($value1, $value2);
        $this->assertEquals(substr($expected, 0, strlen($result)), $result);
    }

    /**
     * @dataProvider ceilExamples
     * @test
     */
    public function it_ceils_a_value($value, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->ceil($value));
    }

    /**
     * @dataProvider floorExamples
     * @test
     */
    public function it_floors_a_value($value, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->floor($value));
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     */
    public function it_calculates_the_absolute_value($value, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->absolute($value));
    }

    /**
     * @dataProvider shareExamples
     * @test
     */
    public function it_shares_a_value($value, $ratio, $total, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->share($value, $ratio, $total));
    }

    /**
     * @dataProvider roundExamples
     * @test
     */
    public function it_rounds_a_value($value, $mode, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->round($value, $mode));
    }

    public function additionExamples()
    {
        return [
            [1, 1, '2'],
            [10, 5, '15'],
        ];
    }

    public function subtractionExamples()
    {
        return [
            [1, 1, '0'],
            [10, 5, '5'],
        ];
    }

    public function multiplicationExamples()
    {
        return [
            [1, 1.5, '1.5'],
            [10, 1.2500, '12.50'],
            [100, 0.29, '29'],
            [100, 0.029, '2.9'],
            [100, 0.0029, '0.29'],
            [1000, 0.29, '290'],
            [1000, 0.029, '29'],
            [1000, 0.0029, '2.9'],
            [2000, 0.0029, '5.8'],
        ];
    }

    public function divisionExamples()
    {
        return [
            [6, 3, '2'],
            [100, 25, '4'],
            [2, 4, '0.5'],
            [20, 0.5, '40'],
            [2, 0.5, '4'],
            [181, 17, '10.64705882352941'],
            [98, 28, '3.5'],
            [98, 25, '3.92'],
            [98, 24, '4.083333333333333'],
            [1, 5.1555, '0.19396760740956'],
        ];
    }

    public function ceilExamples()
    {
        return [
            [1.2, '2'],
            [-1.2, '-1'],
            [2.00, '2'],
        ];
    }

    public function floorExamples()
    {
        return [
            [2.7, '2'],
            [-2.7, '-3'],
            [2.00, '2'],
        ];
    }

    public function absoluteExamples()
    {
        return [
            [2, '2'],
            [-2, '2'],
        ];
    }

    public function shareExamples()
    {
        return [
            [10, 2, 4, '5'],
        ];
    }
}
