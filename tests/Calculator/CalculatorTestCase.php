<?php

namespace Tests\Money\Calculator;

use Money\Calculator;
use Money\Money;

abstract class CalculatorTestCase extends \PHPUnit_Framework_TestCase
{
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
        $this->assertEquals($expected, $this->getCalculator()->divide($value1, $value2));
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
    function it_rounds_a_value($value, $mode, $expected)
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

    public function roundExamples()
    {
        return [
            [2.6, Money::ROUND_HALF_EVEN, '3'],
            [2.5, Money::ROUND_HALF_EVEN, '2'],
            [3.5, Money::ROUND_HALF_EVEN, '4'],
            [-2.6, Money::ROUND_HALF_EVEN, '-3'],
            [-2.5, Money::ROUND_HALF_EVEN, '-2'],
            [-3.5, Money::ROUND_HALF_EVEN, '-4'],
            [2.1, Money::ROUND_HALF_ODD, '2'],
            [2.5, Money::ROUND_HALF_ODD, '3'],
            [3.5, Money::ROUND_HALF_ODD, '3'],
            [-2.1, Money::ROUND_HALF_ODD, '-2'],
            [-2.5, Money::ROUND_HALF_ODD, '-3'],
            [-3.5, Money::ROUND_HALF_ODD, '-3'],
            [2, Money::ROUND_HALF_EVEN, '2'],
            [2, Money::ROUND_HALF_ODD, '2'],
            [-2, Money::ROUND_HALF_ODD, '-2'],
            [2.5, Money::ROUND_HALF_DOWN, '2'],
            [2.6, Money::ROUND_HALF_DOWN, '3'],
            [-2.5, Money::ROUND_HALF_DOWN, '-2'],
            [-2.6, Money::ROUND_HALF_DOWN, '-3'],
            [2.2, Money::ROUND_HALF_UP, '2'],
            [2.5, Money::ROUND_HALF_UP, '3'],
            [2, Money::ROUND_HALF_UP, '2'],
            [-2.5, Money::ROUND_HALF_UP, '-3'],
            [-2, Money::ROUND_HALF_UP, '-2'],
            [2, Money::ROUND_HALF_DOWN, '2'],
            ['12.50', Money::ROUND_HALF_DOWN, '12'],
            ['-12.50', Money::ROUND_HALF_DOWN, '-12'],
            [-1.5, Money::ROUND_HALF_UP, '-2'],
            [-8328.578947368, Money::ROUND_HALF_UP, '-8329'],
            [-8328.5, Money::ROUND_HALF_UP, '-8329'],
            [-8328.5, Money::ROUND_HALF_DOWN, '-8328'],
            [2.5, Money::ROUND_HALF_POSITIVE_INFINITY, '3'],
            [2.6, Money::ROUND_HALF_POSITIVE_INFINITY, '3'],
            [-2.5, Money::ROUND_HALF_POSITIVE_INFINITY, '-2'],
            [-2.6, Money::ROUND_HALF_POSITIVE_INFINITY, '-3'],
            [2, Money::ROUND_HALF_POSITIVE_INFINITY, '2'],
            ['12.50', Money::ROUND_HALF_POSITIVE_INFINITY, '13'],
            ['-12.50', Money::ROUND_HALF_POSITIVE_INFINITY, '-12'],
            [-8328.5, Money::ROUND_HALF_POSITIVE_INFINITY, '-8328'],
            [2.2, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [2.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [2, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [-2.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-3'],
            [-2, Money::ROUND_HALF_NEGATIVE_INFINITY, '-2'],
            [-1.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-2'],
            [-8328.578947368, Money::ROUND_HALF_NEGATIVE_INFINITY, '-8329'],
            [-8328.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-8329'],
        ];
    }
}
