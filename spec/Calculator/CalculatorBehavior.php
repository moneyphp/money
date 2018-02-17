<?php

namespace spec\Money\Calculator;

use Money\Calculator;

/**
 * Mocking with typehints usage won't work here as the trait is autoloaded.
 *
 * @see https://github.com/phpspec/phpspec/issues/825
 */
trait CalculatorBehavior
{
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

    function it_adds_two_values()
    {
        $this->add(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    function it_subtracts_a_value_from_another()
    {
        $this->subtract(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    function it_multiplies_a_value_by_another()
    {
        $this->multiply(rand(-100, 100), rand(-100, 100))->shouldBeString();
    }

    function it_divides_a_value_by_another()
    {
        $this->divide(rand(-100, 100), rand(1, 100))->shouldBeString();
    }

    function it_ceils_a_value()
    {
        $this->ceil(rand(-100, 100) / 100)->shouldBeString();
    }

    function it_floors_a_value()
    {
        $this->floor(rand(-100, 100) / 100)->shouldBeString();
    }

    function it_calculates_the_absolute_value()
    {
        $result = $this->absolute(rand(1, 100));

        $result->shouldBeGreaterThanZero();
        $result->shouldBeString();

        $result = $this->absolute(rand(-100, -1));

        $result->shouldBeGreaterThanZero();
        $result->shouldBeString();
    }

    function it_shares_a_value()
    {
        $this->share(10, 2, 4)->shouldBeString();
    }

    function it_calculates_the_modulus()
    {
        $this->mod(11, 5)->shouldBeString();
    }

    public function getMatchers()
    {
        return [
            'beGreaterThanZero' => function ($subject) {
                return $subject > 0;
            },
        ];
    }
}
