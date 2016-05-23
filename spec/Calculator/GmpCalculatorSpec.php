<?php

namespace spec\Money\Calculator;

use PhpSpec\ObjectBehavior;

class GmpCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Calculator\GmpCalculator');
    }

    function it_adds_with_scale_set()
    {
        $defaultScale = ini_get('bcmath.scale');

        bcscale(1);

        $this->add(1, 1)->shouldReturn('2');

        bcscale($defaultScale);
    }

    function it_subtracts_with_scale_set()
    {
        $defaultScale = ini_get('bcmath.scale');

        bcscale(1);

        $this->subtract(2, 1)->shouldReturn('1');

        bcscale($defaultScale);
    }
}
