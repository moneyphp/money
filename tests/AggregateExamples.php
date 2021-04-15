<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Money;

trait AggregateExamples
{
    /** @psalm-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public function sumExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(30)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-30)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @psalm-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public function minExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(5)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-15)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @psalm-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public function maxExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(15)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-5)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @psalm-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public function avgExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(10)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-10)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }
}
