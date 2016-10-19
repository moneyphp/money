<?php

namespace Tests\Money;

use Money\Number;

final class NumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider numberExamples
     * @test
     */
    public function it_has_attributes($number, $decimal, $half, $currentEven, $negative, $integerPart, $fractionalPart)
    {
        $number = Number::fromString($number);

        $this->assertEquals($decimal, $number->isDecimal());
        $this->assertEquals($half, $number->isHalf());
        $this->assertEquals($currentEven, $number->isCurrentEven());
        $this->assertEquals($negative, $number->isNegative());
        $this->assertEquals($integerPart, $number->getIntegerPart());
        $this->assertEquals($fractionalPart, $number->getFractionalPart());
        $this->assertEquals($negative ? '-1' : '1', $number->getIntegerRoundingMultiplier());
    }

    public function numberExamples()
    {
        return [
            ['0', false, false, true, false, '0', ''],
            ['0.00', false, false, true, false, '0', ''],
            ['0.5', true, true, true, false, '0', '5'],
            ['0.500', true, true, true, false, '0', '5'],
            ['-0', false, false, true, true, '-0', ''],
            ['-0.5', true, true, true, true, '-0', '5'],
            ['3', false, false, false, false, '3', ''],
            ['3.00', false, false, false, false, '3', ''],
            ['3.5', true, true, false, false, '3', '5'],
            ['3.500', true, true, false, false, '3', '5'],
            ['-3', false, false, false, true, '-3', ''],
            ['-3.5', true, true, false, true, '-3', '5'],
            ['10', false, false, true, false, '10', ''],
            ['10.00', false, false, true, false, '10', ''],
            ['10.5', true, true, true, false, '10', '5'],
            ['10.500', true, true, true, false, '10', '5'],
            ['10.9', true, false, true, false, '10', '9'],
            ['-10', false, false, true, true, '-10', ''],
            ['-10.5', true, true, true, true, '-10', '5'],
            ['.5', true, true, true, false, '0', '5'],
            [(string) PHP_INT_MAX, false, false, false, false, (string) PHP_INT_MAX, ''],
            [(string) -PHP_INT_MAX, false, false, false, true, (string) -PHP_INT_MAX, ''],
            [
                PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                false,
                false,
                false,
                false,
                PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                '',
            ],
            [
                -PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                false,
                false,
                false,
                true,
                -PHP_INT_MAX.PHP_INT_MAX.PHP_INT_MAX,
                '',
            ],
            [
                substr(PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1).str_repeat('0', strlen((string) PHP_INT_MAX) - 1).PHP_INT_MAX,
                false,
                false,
                false,
                false,
                substr(PHP_INT_MAX, 0, strlen((string) PHP_INT_MAX) - 1).str_repeat('0', strlen((string) PHP_INT_MAX) - 1).PHP_INT_MAX,
                '',
            ],
        ];
    }
}
