<?php

declare(strict_types=1);

namespace Tests\Money;

use Money\Money;

trait AggregateExamples
{
    /** @phpstan-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public static function sumExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(30)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-30)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @phpstan-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public static function minExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(5)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-15)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @phpstan-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public static function maxExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(15)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-5)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }

    /** @phpstan-return non-empty-list<array{non-empty-list<Money>, Money}> */
    public static function avgExamples(): array
    {
        return [
            [[Money::EUR(5), Money::EUR(10), Money::EUR(15)], Money::EUR(10)],
            [[Money::EUR(-5), Money::EUR(-10), Money::EUR(-15)], Money::EUR(-10)],
            [[Money::EUR(0)], Money::EUR(0)],
        ];
    }
}
