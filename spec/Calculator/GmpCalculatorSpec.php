<?php

namespace spec\Money\Calculator;

use Money\Calculator\GmpCalculator;
use PhpSpec\ObjectBehavior;

class GmpCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType(GmpCalculator::class);
    }
}
