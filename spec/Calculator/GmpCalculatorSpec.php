<?php

declare(strict_types=1);

namespace spec\Money\Calculator;

use Money\Calculator\GmpCalculator;
use PhpSpec\ObjectBehavior;

final class GmpCalculatorSpec extends ObjectBehavior
{
    use CalculatorBehavior;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GmpCalculator::class);
    }
}
