<?php

namespace Tests\Money;

use Money\Money;

/**
 * Mocking with typehints usage won't work here as the trait is autoloaded.
 *
 * @see https://github.com/phpspec/phpspec/issues/825
 */
trait RoundExamples
{
    public function roundExamples()
    {
        return [
            [2.6, Money::ROUND_HALF_EVEN, '3'],
            [2.5, Money::ROUND_HALF_EVEN, '2'],
            [3.5, Money::ROUND_HALF_EVEN, '4'],
            [-2.6, Money::ROUND_HALF_EVEN, '-3'],
            [-2.5, Money::ROUND_HALF_EVEN, '-2'],
            [-3.5, Money::ROUND_HALF_EVEN, '-4'],
            [2.1, Money::ROUND_HALF_ODD, '2'],
            [2.5, Money::ROUND_HALF_ODD, '3'],
            [3.5, Money::ROUND_HALF_ODD, '3'],
            [-2.1, Money::ROUND_HALF_ODD, '-2'],
            [-2.5, Money::ROUND_HALF_ODD, '-3'],
            [-3.5, Money::ROUND_HALF_ODD, '-3'],
            [2, Money::ROUND_HALF_EVEN, '2'],
            [2, Money::ROUND_HALF_ODD, '2'],
            [-2, Money::ROUND_HALF_ODD, '-2'],
            [2.5, Money::ROUND_HALF_DOWN, '2'],
            [2.6, Money::ROUND_HALF_DOWN, '3'],
            [-2.5, Money::ROUND_HALF_DOWN, '-2'],
            [-2.6, Money::ROUND_HALF_DOWN, '-3'],
            [2.2, Money::ROUND_HALF_UP, '2'],
            [2.5, Money::ROUND_HALF_UP, '3'],
            [2, Money::ROUND_HALF_UP, '2'],
            [-2.5, Money::ROUND_HALF_UP, '-3'],
            [-2, Money::ROUND_HALF_UP, '-2'],
            [2, Money::ROUND_HALF_DOWN, '2'],
            ['12.50', Money::ROUND_HALF_DOWN, '12'],
            ['-12.50', Money::ROUND_HALF_DOWN, '-12'],
            [-1.5, Money::ROUND_HALF_UP, '-2'],
            [-8328.578947368, Money::ROUND_HALF_UP, '-8329'],
            [-8328.5, Money::ROUND_HALF_UP, '-8329'],
            [-8328.5, Money::ROUND_HALF_DOWN, '-8328'],
            [2.5, Money::ROUND_HALF_POSITIVE_INFINITY, '3'],
            [2.6, Money::ROUND_HALF_POSITIVE_INFINITY, '3'],
            [-2.5, Money::ROUND_HALF_POSITIVE_INFINITY, '-2'],
            [-2.6, Money::ROUND_HALF_POSITIVE_INFINITY, '-3'],
            [2, Money::ROUND_HALF_POSITIVE_INFINITY, '2'],
            ['12.50', Money::ROUND_HALF_POSITIVE_INFINITY, '13'],
            ['-12.50', Money::ROUND_HALF_POSITIVE_INFINITY, '-12'],
            [-8328.5, Money::ROUND_HALF_POSITIVE_INFINITY, '-8328'],
            [2.2, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [2.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [2, Money::ROUND_HALF_NEGATIVE_INFINITY, '2'],
            [-2.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-3'],
            [-2, Money::ROUND_HALF_NEGATIVE_INFINITY, '-2'],
            [-1.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-2'],
            [-8328.578947368, Money::ROUND_HALF_NEGATIVE_INFINITY, '-8329'],
            [-8328.5, Money::ROUND_HALF_NEGATIVE_INFINITY, '-8329'],
        ];
    }
}
