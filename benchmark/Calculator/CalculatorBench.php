<?php

declare(strict_types=1);

namespace Benchmark\Money\Calculator;

use Money\Calculator;
use Money\Money;

abstract class CalculatorBench
{
    /**
     * @psalm-return class-string<Calculator>
     */
    abstract protected function getCalculator(): string;

    public function benchCompare(): void
    {
        $this->getCalculator()::compare('1', '1');
        $this->getCalculator()::compare('1', '5');
        $this->getCalculator()::compare('5', '5');
        $this->getCalculator()::compare('5.5', '1.5');
        $this->getCalculator()::compare('1.5', '5.5');
    }

    public function benchAdd(): void
    {
        $this->getCalculator()::add('1', '5');
    }

    public function benchSubtract(): void
    {
        $this->getCalculator()::subtract('1', '5');
    }

    public function benchMultiply(): void
    {
        $this->getCalculator()::multiply('5', '25');
        $this->getCalculator()::multiply('5', '1.5');
    }

    public function benchDivide(): void
    {
        $this->getCalculator()::divide('5', '4');
    }

    public function benchCeil(): void
    {
        $this->getCalculator()::ceil('5.5');
    }

    public function benchFloor(): void
    {
        $this->getCalculator()::floor('5.5');
    }

    public function benchAbsolute(): void
    {
        $this->getCalculator()::absolute('5');
        $this->getCalculator()::absolute('-5');
    }

    public function benchRound(): void
    {
        $this->getCalculator()::round('2.6', Money::ROUND_HALF_EVEN);
    }

    public function benchShare(): void
    {
        $this->getCalculator()::share('10', '2', '4');
    }

    public function benchMod(): void
    {
        $this->getCalculator()::mod('11', '5');
    }
}
