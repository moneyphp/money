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

final class BcMathCalculator implements Calculator
{
    private int $scale;

    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
    }

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

    public function multiply(string $amount, int|float|string $multiplier): string
    {
        return bcmul($amount, (string) $multiplier, $this->scale);
    }

    public function divide(string $amount, int|float|string $divisor): string
    {
        return bcdiv($amount, (string) $divisor, $this->scale);
    }

    public function ceil(string $number): string
    {
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
            return (string) $number;
        }

        if ($number->isNegative()) {
            return bcadd((string) $number, '0', 0);
        }

        return bcadd((string) $number, '1', 0);
    }

    public function floor(string $number): string
    {
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
            return (string) $number;
        }

        if ($number->isNegative()) {
            return bcadd((string) $number, '-1', 0);
        }

        return bcadd($number->__toString(), '0', 0);
    }

    public function absolute(string $number): string
    {
        return ltrim($number, '-');
    }

    public function round(int|float|string $number, int $roundingMode): string
    {
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
            return (string) $number;
        }

        if ($number->isHalf() === false) {
            return $this->roundDigit($number);
        }

        if ($roundingMode === Money::ROUND_HALF_UP) {
            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_DOWN) {
            return bcadd((string) $number, '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_EVEN) {
            if ($number->isCurrentEven()) {
                return bcadd((string) $number, '0', 0);
            }

            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven()) {
                return bcadd(
                    (string) $number,
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd((string) $number, '0', 0);
        }

        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd((string) $number, '0', 0);
            }

            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            if ($number->isNegative()) {
                return bcadd(
                    (string) $number,
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd(
                (string) $number,
                '0',
                0
            );
        }

        throw new InvalidArgumentException('Unknown rounding mode');
    }

    private function roundDigit(Number $number): string
    {
        if ($number->isCloserToNext()) {
            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        return bcadd((string) $number, '0', 0);
    }

    public function share(string $amount, int|float|string $ratio, int|float|string $total): string
    {
        return $this->floor(bcdiv(bcmul($amount, (string) $ratio, $this->scale), (string) $total, $this->scale));
    }

    public function mod(string $amount, int|float|string $divisor): string
    {
        return bcmod($amount, $divisor);
    }
}
