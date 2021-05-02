<?php

declare(strict_types=1);

namespace spec\Money\Calculator;

use Money\Calculator\BcMathCalculator;
use PhpSpec\ObjectBehavior;

final class BcMathCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BcMathCalculator::class);
    }
}
