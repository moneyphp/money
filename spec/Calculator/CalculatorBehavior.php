<?php

namespace spec\Money\Calculator;

use Money\Calculator;
use spec\Money\RoundExamples;

/**
 * Mocking with typehints usage won't work here as the trait is autoloaded.
 *
 * @see https://github.com/phpspec/phpspec/issues/825
 */
trait CalculatorBehavior
{
    use RoundExamples;

    function it_is_a_calculator()
    {
        $this->shouldImplement(Calculator::class);
    }

    function it_compares_two_values()
    {
        $this->compare(2, 1)->shouldReturn(1);
        $this->compare(1, 2)->shouldReturn(-1);
        $this->compare(1, 1)->shouldReturn(0);
    }

    function it_adds_a_value()
    {
        $this->add(1, 1)->shouldReturn('2');
    }

    function it_subtracts_a_value()
    {
        $this->subtract(2, 1)->shouldReturn('1');
    }

    // TODO Examine comparison
    function it_multiplies_a_value()
    {
        $this->multiply(1, 1.5)->shouldReturn('1.5');
        $this->multiply(10, 1.2500)->shouldBeLike('12.50');
    }

    // TODO Examine comparison
    function it_divides_a_value()
    {
        $this->divide(3, 2)->shouldBeLike('1.5');
        $this->divide(10, 4)->shouldBeLike('2.5');
    }

    function it_ceils_a_value()
    {
        $this->ceil(1.2)->shouldReturn('2');
        $this->ceil(-1.2)->shouldReturn('-1');
        $this->ceil('2.00')->shouldReturn('2');
    }

    function it_floors_a_value()
    {
        $this->floor(2.7)->shouldReturn('2');
        $this->floor(-2.7)->shouldReturn('-3');
        $this->floor('2.00')->shouldReturn('2');
    }

    function it_calculates_the_absolute_value()
    {
        $this->absolute(2)->shouldReturn('2');
        $this->absolute(-2)->shouldReturn('2');
    }

    function testShare()
    {
        $this->share(10, 2, 4)->shouldReturn('5');
    }

    /**
     *  @dataProvider roundExamples
     */
    function it_rounds_a_value($input, $mode, $expected)
    {
        $this->round($input, $mode)->shouldReturn($expected);
    }
}
