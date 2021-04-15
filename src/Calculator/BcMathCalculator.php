<?php

declare(strict_types=1);

namespace Money\Calculator;

use InvalidArgumentException;
use Money\Calculator;
use Money\Money;
use Money\Number;

use function bcadd;
use function bccomp;
use function bcdiv;
use function bcmod;
use function bcmul;
use function bcsub;
use function extension_loaded;
use function ltrim;

/** @psalm-immutable */
final class BcMathCalculator implements Calculator
{
    private int $scale;

    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
    }

    /** @psalm-pure */
    public static function supported(): bool
    {
        return extension_loaded('bcmath');
    }

    public function compare(string $a, string $b): int
    {
        return bccomp($a, $b, $this->scale);
    }

    public function add(string $amount, string $addend): string
    {
        return bcadd($amount, $addend, $this->scale);
    }

    public function subtract(string $amount, string $subtrahend): string
    {
        return bcsub($amount, $subtrahend, $this->scale);
    }

    public function multiply(string $amount, string $multiplier): string
    {
        return bcmul($amount, $multiplier, $this->scale);
    }

    public function divide(string $amount, string $divisor): string
    {
        return bcdiv($amount, $divisor, $this->scale);
    }

    public function ceil(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return bcadd($number->__toString(), '0', 0);
        }

        return bcadd($number->__toString(), '1', 0);
    }

    public function floor(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return bcadd($number->__toString(), '-1', 0);
        }

        return bcadd($number->__toString(), '0', 0);
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
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isHalf() === false) {
            return $this->roundDigit($number);
        }

        if ($roundingMode === Money::ROUND_HALF_UP) {
            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_DOWN) {
            return bcadd($number->__toString(), '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_EVEN) {
            if ($number->isCurrentEven()) {
                return bcadd($number->__toString(), '0', 0);
            }

            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven()) {
                return bcadd(
                    $number->__toString(),
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd($number->__toString(), '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd($number->__toString(), '0', 0);
            }

            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd(
                    $number->__toString(),
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd(
                $number->__toString(),
                '0',
                0
            );
        }

        throw new InvalidArgumentException('Unknown rounding mode');
    }

    /** @psalm-return numeric-string */
    private function roundDigit(Number $number): string
    {
        if ($number->isCloserToNext()) {
            return bcadd(
                $number->__toString(),
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        return bcadd($number->__toString(), '0', 0);
    }

    public function share(string $amount, string $ratio, string $total): string
    {
        return $this->floor(bcdiv(bcmul($amount, $ratio, $this->scale), $total, $this->scale));
    }

    public function mod(string $amount, string $divisor): string
    {
        // @TODO: null check needed because `bcmod(_, 0)` fails - should the check be kept in here?
        return bcmod($amount, $divisor) ?? '0';
    }
}
