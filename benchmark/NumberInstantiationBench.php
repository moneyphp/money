<?php

declare(strict_types=1);

namespace Benchmark\Money;

use Money\Number;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;

final class NumberInstantiationBench
{

    public function benchConstructorWithZeroIntegerAmount(): void
    {
        new Number('0', '');
    }

    public function benchConstructorWithPositiveIntegerAmount(): void
    {
        new Number('1234567890', '');
    }

    public function benchConstructorWithNegativeIntegerAmount(): void
    {
        new Number('-1234567890', '');
    }

    public function benchConstructorWithZeroAndFractionalAmount(): void
    {
        new Number('0', '1234567890');
    }

    public function benchConstructorWithFractionalAmount(): void
    {
        new Number('1234567890', '1234567890');
    }

    public function benchConstructorWithNegativeFractionalAmount(): void
    {
        new Number('-1234567890', '1234567890');
    }

    public function benchFromStringWithZeroIntegerAmount(): void
    {
        Number::fromString('0');
    }

    public function benchFromStringWithPositiveIntegerAmount(): void
    {
        Number::fromString('1234567890');
    }

    public function benchFromStringWithNegativeIntegerAmount(): void
    {
        Number::fromString('-1234567890');
    }

    public function benchFromStringWithZeroAndFractionalAmount(): void
    {
        Number::fromString('0.1234567890');
    }

    public function benchFromStringWithFractionalAmount(): void
    {
        Number::fromString('1234567890.1234567890');
    }

    public function benchFromStringWithNegativeFractionalAmount(): void
    {
        Number::fromString('-1234567890.1234567890');
    }
}
