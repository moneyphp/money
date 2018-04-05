<?php

namespace Tests\Money;

use Money\Money;

trait AggregateExamples
{
    public function sumExamples()
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(30)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-30)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function minExamples()
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(5)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-15)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function maxExamples()
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(15)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-5)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    public function avgExamples()
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(10)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-10)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }
}
