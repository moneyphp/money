<?php

namespace spec\Money\Calculator;

use PhpSpec\ObjectBehavior;

class BcMathCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Calculator\BcMathCalculator');
    }
}
