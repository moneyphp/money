<?php

namespace Tests\Money\Calculator;

use Money\Calculator;
use PHPUnit\Framework\TestCase;
use Tests\Money\RoundExamples;

abstract class CalculatorTestCase extends TestCase
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
        $this->assertEquals(substr($expected, 0, 12), substr($result, 0, 12));
    }

    /**
     * @dataProvider divisionExactExamples
     * @test
     */
    public function it_divides_a_value_by_another_exact($value1, $value2, $expected)
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
    public function it_rounds_a_value($value, $mode, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->round($value, $mode));
    }

    /**
     * @dataProvider compareLessExamples
     * @test
     */
    public function it_compares_values_less($left, $right)
    {
        // Compare with both orders. One must return a value less than zero,
        // the other must return a value greater than zero.
        $this->assertLessThan(0, $this->getCalculator()->compare($left, $right));
        $this->assertGreaterThan(0, $this->getCalculator()->compare($right, $left));
    }

    /**
     * @dataProvider compareEqualExamples
     * @test
     */
    public function it_compares_values($left, $right)
    {
        // Compare with both orders, both must return zero.
        $this->assertEquals(0, $this->getCalculator()->compare($left, $right));
        $this->assertEquals(0, $this->getCalculator()->compare($right, $left));
    }

    /**
     * @dataProvider modExamples
     * @test
     */
    public function it_calculates_the_modulus_of_a_value($left, $right, $expected)
    {
        $this->assertEquals($expected, $this->getCalculator()->mod($left, $right));
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
            ['1', 0.006597, '0.006597'],
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
            ['-500', 110, '-4.54545454545455'],
        ];
    }

    public function divisionExactExamples()
    {
        return [
            [6, 3, '2'],
            [100, 25, '4'],
            [2, 4, '0.5'],
            [20, 0.5, '40'],
            [2, 0.5, '4'],
            [98, 28, '3.5'],
            [98, 25, '3.92'],
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

    public function compareLessExamples()
    {
        return [
            [0, 1],
            ['0', '1'],
            ['0.0005', '1'],
            ['0.000000000000000000000000005', '1'],
            ['-1000', '1000', -1],
        ];
    }

    public function compareEqualExamples()
    {
        return [
            [1, 1],
            ['1', '1'],
            ['-1000', '-1000'],
        ];
    }

    public function modExamples()
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
            [-13, -5, '-3'],
            [-13, 5, '-3'],
            [13, -5, '3'],
        ];
    }
}
