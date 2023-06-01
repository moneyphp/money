<?php

declare(strict_types=1);

namespace Benchmark\Money;

use Money\Currency;
use Money\Money;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;

/**
 * @BeforeMethods({"setUp"})
 */
final class MoneyInstantiationBench
{
    /** @var Currency */
    private $currency;

    public function setUp(): void
    {
        $this->currency = new Currency('EUR');
    }

    public function benchConstructorWithZeroIntegerAmount(): void
    {
        new Money(0, $this->currency);
    }

    public function benchConstructorWithPositiveIntegerAmount(): void
    {
        new Money(1234567890, $this->currency);
    }

    public function benchConstructorWithNegativeIntegerAmount(): void
    {
        new Money(-1234567890, $this->currency);
    }

    public function benchConstructorWithZeroStringAmount(): void
    {
        new Money('0', $this->currency);
    }

    public function benchConstructorWithPositiveStringAmount(): void
    {
        new Money('1234567890', $this->currency);
    }

    public function benchConstructorWithNegativeStringAmount(): void
    {
        new Money('-1234567890', $this->currency);
    }
}
