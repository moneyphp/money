<?php

namespace Tests\Money\Calculator;

use Money\Calculator\PhpCalculator;

final class PhpCalculatorTest extends CalculatorTestCase
{
    protected function getCalculator()
    {
        return new PhpCalculator();
    }

    /**
     * @dataProvider multiplicationExamples
     * @test
     */
    public function it_multiplies_a_value_by_another_when_decimal_separator_is_comma($value1, $value2, $expected)
    {
        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');

        $this->assertEquals($expected, $this->getCalculator()->multiply($value1, $value2));
    }

    /**
     * @dataProvider divisionExamples
     * @test
     */
    public function it_divides_a_value_by_another_when_decimal_separator_is_comma($value1, $value2, $expected)
    {
        $this->setLocale(LC_ALL, 'ru_RU.UTF-8');

        $result = $this->getCalculator()->divide($value1, $value2);
        $this->assertEquals(substr($expected, 0, strlen($result)), $result);
    }
}
