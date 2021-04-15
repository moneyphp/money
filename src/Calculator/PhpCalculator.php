<?php

declare(strict_types=1);

namespace Money\Calculator;

use Money\Calculator;
use Money\Money;
use Money\Number;
use OverflowException;
use UnderflowException;
use UnexpectedValueException;

use function ceil;
use function filter_var;
use function floor;
use function intval;
use function ltrim;
use function round;

use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;

final class PhpCalculator implements Calculator
{
    public static function supported(): bool
    {
        return true;
    }

    public function compare(string $a, string $b): int
    {
        return $a < $b ? -1 : ($a > $b ? 1 : 0);
    }

    public function add(string $amount, string $addend): string
    {
        $result = $amount + $addend;

        $this->assertInteger($result);

        return (string) $result;
    }

    public function subtract(string $amount, string $subtrahend): string
    {
        $result = $amount - $subtrahend;

        $this->assertInteger($result);

        return (string) $result;
    }

    public function multiply(string $amount, int|float|string $multiplier): string
    {
        $result = $amount * $multiplier;

        $this->assertIntegerBounds($result);

        return (string) Number::fromNumber($result);
    }

    public function divide(string $amount, int|float|string $divisor): string
    {
        $result = $amount / $divisor;

        $this->assertIntegerBounds($result);

        return (string) Number::fromNumber($result);
    }

    public function ceil(string $number): string
    {
        return $this->castInteger(ceil((float) $number));
    }

    public function floor(string $number): string
    {
        return $this->castInteger(floor((float) $number));
    }

    public function absolute(string $number): string
    {
        return ltrim($number, '-');
    }

    public function round(int|float|string $number, int $roundingMode): string
    {
        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            $number = Number::fromNumber($number);

            if ($number->isHalf()) {
                return $this->castInteger(ceil((float) $number->__toString()));
            }

            return $this->castInteger(round((float) $number->__toString(), 0, Money::ROUND_HALF_UP));
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            $number = Number::fromNumber($number);

            if ($number->isHalf()) {
                return $this->castInteger(floor((float) $number->__toString()));
            }

            return $this->castInteger(round((float) $number->__toString(), 0, Money::ROUND_HALF_DOWN));
        }

        return $this->castInteger(round((float) $number, 0, $roundingMode));
    }

    public function share(string $amount, int|float|string $ratio, int|float|string $total): string
    {
        return $this->castInteger(floor($amount * $ratio / $total));
    }

    public function mod(string $amount, int|float|string $divisor): string
    {
        $result = $amount % $divisor;

        $this->assertIntegerBounds($result);

        return (string) $result;
    }

    /**
     * Asserts that an integer value didn't become something else
     * (after some arithmetic operation).
     *
     * @throws OverflowException  If integer overflow occured.
     * @throws UnderflowException If integer underflow occured.
     */
    private function assertIntegerBounds(int|float $amount): void
    {
        if ($amount > PHP_INT_MAX) {
            throw new OverflowException('You overflowed the maximum allowed integer (PHP_INT_MAX)');
        }

        if ($amount < ~PHP_INT_MAX) {
            throw new UnderflowException('You underflowed the minimum allowed integer (PHP_INT_MAX)');
        }
    }

    /**
     * Casts an amount to integer ensuring that an overflow/underflow did not occur.
     */
    private function castInteger(int|float $amount): string
    {
        $this->assertIntegerBounds($amount);

        return (string) intval($amount);
    }

    /**
     * Asserts that integer remains integer after arithmetic operations.
     */
    private function assertInteger(int $amount): void
    {
        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }
}
