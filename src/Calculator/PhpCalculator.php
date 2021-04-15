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
use function ltrim;
use function round;

use const FILTER_VALIDATE_INT;
use const PHP_INT_MAX;

/** @psalm-immutable */
final class PhpCalculator implements Calculator
{
    /** @psalm-pure */
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

    public function multiply(string $amount, string $multiplier): string
    {
        $result = $amount * $multiplier;

        $this->assertIntegerBounds($result);

        return (string) Number::fromNumber($result);
    }

    public function divide(string $amount, string $divisor): string
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

    /**
     * @psalm-suppress MoreSpecificReturnType we know that trimming `-` produces a positive numeric-string here
     * @psalm-suppress LessSpecificReturnStatement we know that trimming `-` produces a positive numeric-string here
     */
    public function absolute(string $number): string
    {
        return ltrim($number, '-');
    }

    /**
     * @psalm-param Money::ROUND_* $roundingMode
     *
     * @psalm-return numeric-string
     */
    public function round(string $number, int $roundingMode): string
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

        /** @psalm-suppress MixedArgument the type of $roundingMode is well known, but inference fails on vimeo/psalm:4.7.0@d4377c0baf3ffbf0b1ec6998e8d1be2a40971005 */
        return $this->castInteger(round((float) $number, 0, $roundingMode));
    }

    public function share(string $amount, string $ratio, string $total): string
    {
        return $this->castInteger(floor($amount * $ratio / $total));
    }

    public function mod(string $amount, string $divisor): string
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
     *
     * @psalm-return numeric-string
     */
    private function castInteger(int|float $amount): string
    {
        $this->assertIntegerBounds($amount);

        return (string) (int) $amount;
    }

    /** Asserts that integer remains integer after arithmetic operations. */
    private function assertInteger(string|float|int $amount): void
    {
        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }
}
