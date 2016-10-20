<?php

namespace spec\Money\Calculator;

use Money\Calculator\BcMathCalculator;
use PhpSpec\ObjectBehavior;

final class BcMathCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    function it_is_initializable()
    {
        $this->shouldHaveType(BcMathCalculator::class);
    }
}
