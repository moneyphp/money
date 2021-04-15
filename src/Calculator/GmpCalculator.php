<?php

declare(strict_types=1);

namespace Money\Calculator;

use InvalidArgumentException;
use Money\Calculator;
use Money\Money;
use Money\Number;

use function extension_loaded;
use function gmp_add;
use function gmp_cmp;
use function gmp_div_q;
use function gmp_div_qr;
use function gmp_init;
use function gmp_mod;
use function gmp_mul;
use function gmp_neg;
use function gmp_strval;
use function gmp_sub;
use function ltrim;
use function str_pad;
use function strlen;
use function substr;

use const GMP_ROUND_MINUSINF;
use const STR_PAD_LEFT;

/** @psalm-immutable */
final class GmpCalculator implements Calculator
{
    private int $scale;

    /** @psalm-param positive-int|0 $scale */
    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
    }

    /** @psalm-pure */
    public static function supported(): bool
    {
        return extension_loaded('gmp');
    }

    public function compare(string $a, string $b): int
    {
        $aNum = Number::fromString($a);
        $bNum = Number::fromString($b);

        if ($aNum->isDecimal() || $bNum->isDecimal()) {
            $integersCompared = gmp_cmp($aNum->getIntegerPart(), $bNum->getIntegerPart());
            if ($integersCompared !== 0) {
                return $integersCompared;
            }

            $aNumFractional = $aNum->getFractionalPart() === '' ? '0' : $aNum->getFractionalPart();
            $bNumFractional = $bNum->getFractionalPart() === '' ? '0' : $bNum->getFractionalPart();

            return gmp_cmp($aNumFractional, $bNumFractional);
        }

        return gmp_cmp($a, $b);
    }

    public function add(string $amount, string $addend): string
    {
        return gmp_strval(gmp_add($amount, $addend));
    }

    public function subtract(string $amount, string $subtrahend): string
    {
        return gmp_strval(gmp_sub($amount, $subtrahend));
    }

    public function multiply(string $amount, string $multiplier): string
    {
        $multiplier = Number::fromString($multiplier);

        if ($multiplier->isDecimal()) {
            $decimalPlaces  = strlen($multiplier->getFractionalPart());
            $multiplierBase = $multiplier->getIntegerPart();

            if ($multiplierBase) {
                $multiplierBase .= $multiplier->getFractionalPart();
            } else {
                $multiplierBase = ltrim($multiplier->getFractionalPart(), '0');
            }

            $resultBase = gmp_strval(gmp_mul(gmp_init($amount), gmp_init($multiplierBase)));

            if ($resultBase === '0') {
                return '0';
            }

            $result       = substr($resultBase, $decimalPlaces * -1);
            $resultLength = strlen($result);
            if ($decimalPlaces > $resultLength) {
                /** @psalm-var numeric-string $finalResult */
                $finalResult = '0.' . str_pad('', $decimalPlaces - $resultLength, '0') . $result;

                return $finalResult;
            }

            /** @psalm-var numeric-string $finalResult */
            $finalResult = substr($resultBase, 0, $decimalPlaces * -1) . '.' . $result;

            return $finalResult;
        }

        return gmp_strval(gmp_mul(gmp_init($amount), gmp_init((string) $multiplier)));
    }

    public function divide(string $amount, string $divisor): string
    {
        $divisor = Number::fromString($divisor);

        if ($divisor->isDecimal()) {
            $decimalPlaces = strlen($divisor->getFractionalPart());

            if ($divisor->getIntegerPart()) {
                $divisor = new Number($divisor->getIntegerPart() . $divisor->getFractionalPart());
            } else {
                $divisor = new Number(ltrim($divisor->getFractionalPart(), '0'));
            }

            $amount = gmp_strval(gmp_mul(gmp_init($amount), gmp_init('1' . str_pad('', $decimalPlaces, '0'))));
        }

        [$integer, $remainder] = gmp_div_qr(gmp_init($amount), gmp_init((string) $divisor));

        if (gmp_cmp($remainder, '0') === 0) {
            return gmp_strval($integer);
        }

        $divisionOfRemainder = gmp_strval(
            gmp_div_q(
                gmp_mul($remainder, gmp_init('1' . str_pad('', $this->scale, '0'))),
                gmp_init((string) $divisor),
                GMP_ROUND_MINUSINF
            )
        );

        if ($divisionOfRemainder[0] === '-') {
            $divisionOfRemainder = substr($divisionOfRemainder, 1);
        }

        /** @psalm-var numeric-string $finalResult */
        $finalResult = gmp_strval($integer) . '.' . str_pad($divisionOfRemainder, $this->scale, '0', STR_PAD_LEFT);

        return $finalResult;
    }

    public function ceil(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return $this->add($number->getIntegerPart(), '0');
        }

        return $this->add($number->getIntegerPart(), '1');
    }

    public function floor(string $number): string
    {
        $number = Number::fromString($number);

        if ($number->isInteger()) {
            return $number->__toString();
        }

        if ($number->isNegative()) {
            return $this->add($number->getIntegerPart(), '-1');
        }

        return $this->add($number->getIntegerPart(), '0');
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
            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if ($roundingMode === Money::ROUND_HALF_DOWN) {
            return $this->add($number->getIntegerPart(), '0');
        }

        if ($roundingMode === Money::ROUND_HALF_EVEN) {
            if ($number->isCurrentEven()) {
                return $this->add($number->getIntegerPart(), '0');
            }

            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven()) {
                return $this->add(
                    $number->getIntegerPart(),
                    $number->getIntegerRoundingMultiplier()
                );
            }

            return $this->add($number->getIntegerPart(), '0');
        }

        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            if ($number->isNegative()) {
                return $this->add(
                    $number->getIntegerPart(),
                    '0'
                );
            }

            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if ($roundingMode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            if ($number->isNegative()) {
                return $this->add(
                    $number->getIntegerPart(),
                    $number->getIntegerRoundingMultiplier()
                );
            }

            return $this->add(
                $number->getIntegerPart(),
                '0'
            );
        }

        throw new InvalidArgumentException('Unknown rounding mode');
    }

    /** @psalm-return numeric-string */
    private function roundDigit(Number $number): string
    {
        if ($number->isCloserToNext()) {
            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        return $this->add($number->getIntegerPart(), '0');
    }

    public function share(string $amount, string $ratio, string $total): string
    {
        return $this->floor($this->divide($this->multiply($amount, $ratio), $total));
    }

    public function mod(string $amount, string $divisor): string
    {
        // gmp_mod() only calculates non-negative integers, so we use absolutes
        $remainder = gmp_mod($this->absolute($amount), $this->absolute($divisor));

        // If the amount was negative, we negate the result of the modulus operation
        $amount = Number::fromString($amount);

        if ($amount->isNegative()) {
            $remainder = gmp_neg($remainder);
        }

        return gmp_strval($remainder);
    }
}
