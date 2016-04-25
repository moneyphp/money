<?php

namespace spec\Money;

use Money\Number;
use PhpSpec\ObjectBehavior;

class NumberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('1');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Number');
    }

    function it_throws_an_exception_when_number_is_not_string()
    {
        $this->beConstructedWith(1);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_creates_a_number_from_float()
    {
        $number = $this->fromFloat(1.1);

        $number->shouldHaveType(Number::class);
        $number->__toString()->shouldReturn('1.1');
    }

    function it_throws_an_exception_when_number_is_not_float_during_creation_from_float()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringFromFloat(1);
    }

    /**
     * @dataProvider numberExamples
     */
    function it_has_attributes($number, $decimal, $half, $currentEven, $negative, $integerPart, $fractionalPart)
    {
        $this->beConstructedWith($number);

        $this->isDecimal()->shouldReturn($decimal);
        $this->isHalf()->shouldReturn($half);
        $this->isCurrentEven()->shouldReturn($currentEven);
        $this->isNegative()->shouldReturn($negative);
        $this->getIntegerPart()->shouldReturn($integerPart);
        $this->getFractionalPart()->shouldReturn($fractionalPart);
        $this->getIntegerRoundingMultiplier()->shouldReturn($negative ? '-1' : '1');
    }

    public function numberExamples()
    {
        return [
            ['0', false, false, true, false, '0', ''],
            ['0.00', true, false, true, false, '0', ''],
            ['0.5', true, true, true, false, '0', '5'],
            ['0.500', true, true, true, false, '0', '5'],
            ['-0', false, false, true, true, '-0', ''],
            ['-0.5', true, true, true, true, '-0', '5'],
            ['3', false, false, false, false, '3', ''],
            ['3.00', true, false, false, false, '3', ''],
            ['3.5', true, true, false, false, '3', '5'],
            ['3.500', true, true, false, false, '3', '5'],
            ['-3', false, false, false, true, '-3', ''],
            ['-3.5', true, true, false, true, '-3', '5'],
            ['10', false, false, true, false, '10', ''],
            ['10.00', true, false, true, false, '10', ''],
            ['10.5', true, true, true, false, '10', '5'],
            ['10.500', true, true, true, false, '10', '5'],
            ['10.9', true, false, true, false, '10', '9'],
            ['-10', false, false, true, true, '-10', ''],
            ['-10.5', true, true, true, true, '-10', '5'],
            [(string) PHP_INT_MAX, false, false, false, false, (string) PHP_INT_MAX, ''],
            [(string) -PHP_INT_MAX, false, false, false, true, (string) -PHP_INT_MAX, ''],
        ];
    }
}
