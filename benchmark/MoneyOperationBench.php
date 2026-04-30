<?php

declare(strict_types=1);

namespace Benchmark\Money;

use Money\Currency;
use Money\Money;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;

/**
 * @BeforeMethods({"setUp"})
 */
final class MoneyOperationBench
{
    /** @var Money */
    private $a;
    /** @var Money */
    private $b;

    public function setUp(): void
    {
        $currency = new Currency('EUR');
        $this->a = new Money('100', $currency);
        $this->b = new Money('50', $currency);
    }

    public function benchAdd(): void
    {
        $this->a->add($this->b);
    }

    public function benchSubtract(): void
    {
        $this->a->subtract($this->b);
    }

    public function benchMultiply(): void
    {
        $this->a->multiply('5');
    }

    public function benchDivide(): void
    {
        $this->a->divide('5');
    }

    public function benchSum(): void
    {
        Money::sum($this->a, $this->b, $this->a, $this->b);
    }

    public function benchMin(): void
    {
        Money::min($this->a, $this->b, $this->a, $this->b);
    }

    public function benchMax(): void
    {
        Money::max($this->a, $this->b, $this->a, $this->b);
    }

    public function benchAvg(): void
    {
        Money::avg($this->a, $this->b, $this->a, $this->b);
    }

    public function benchRatioOf(): void
    {
        $this->a->ratioOf($this->b);
    }

    public function benchMod(): void
    {
        $this->a->mod($this->b);
    }

    public function benchIsSameCurrency(): void
    {
        $this->a->isSameCurrency($this->b);
    }

    public function benchIsZero(): void
    {
        $this->a->isZero();
    }

    public function benchAbsolute(): void
    {
        $this->a->absolute();
    }

    public function benchNegative(): void
    {
        $this->a->negative();
    }

    public function benchIsPositive(): void
    {
        $this->a->isPositive();
    }

    public function benchCompare(): void
    {
        $this->a->compare($this->b);
    }

    public function benchLessThan(): void
    {
        $this->a->lessThan($this->b);
    }

    public function benchLessThanOrEqual(): void
    {
        $this->a->lessThanOrEqual($this->b);
    }

    public function benchEquals(): void
    {
        $this->a->equals($this->b);
    }

    public function benchGreaterThan(): void
    {
        $this->a->greaterThan($this->b);
    }

    public function benchGreaterThanOrEqual(): void
    {
        $this->a->greaterThanOrEqual($this->b);
    }
}
