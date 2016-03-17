<?php

namespace Money\Calculator;

use Money\Calculator;
use Money\Money;
use Money\Number;

/**
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class GmpCalculator implements Calculator
{
    /**
     * {@inheritdoc}
     */
    public static function supported()
    {
        return extension_loaded('gmp');
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        return gmp_cmp($a, $b);
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        return gmp_strval(gmp_add($amount, $addend));
    }

    /**
     * {@inheritdoc}
     */
    public function subtract($amount, $subtrahend)
    {
        return gmp_strval(gmp_sub($amount, $subtrahend));
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier)
    {
        $multiplier = new Number((string) $multiplier);

        if ($multiplier->isDecimal()) {
            $decimalPlaces = strlen($multiplier->getFractionalPart());
            $multiplierBase = $multiplier->getIntegerPart();
            if ($multiplierBase) {
                $multiplierBase .= $multiplier->getFractionalPart();
            } else {
                $multiplierBase = $multiplier->getFractionalPart();
            }

            $resultBase = gmp_strval(gmp_mul(gmp_init($amount), gmp_init($multiplierBase)));
            $resultLength = strlen($resultBase);
            $result = substr($resultBase, 0, $resultLength - $decimalPlaces);
            $result .= '.'.substr($resultBase, $resultLength - $decimalPlaces);

            return $result;
        }

        return gmp_strval(gmp_mul(gmp_init($amount), gmp_init((string) $multiplier)));
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        return $this->multiply($amount, 1 / $divisor);
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        $number = new Number((string) $number);

        if ($number->isDecimal() === false) {
            return (string) $number;
        }

        if ($number->isNegative() === true) {
            return $this->add($number->getIntegerPart(), '0');
        }

        return $this->add($number->getIntegerPart(), '1');
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        $number = new Number((string) $number);

        if ($number->isDecimal() === false) {
            return (string) $number;
        }

        if ($number->isNegative() === true) {
            return $this->add($number->getIntegerPart(), '-1');
        }

        return $this->add($number->getIntegerPart(), '0');
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $roundingMode)
    {
        $number = new Number((string) $number);
        if ($number->isDecimal() === false) {
            return (string) $number;
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
            if ($number->isCurrentEven() === true) {
                return $this->add($number->getIntegerPart(), '0');
            }

            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven() === true) {
                return $this->add(
                    $number->getIntegerPart(),
                    $number->getIntegerRoundingMultiplier()
                );
            }

            return $this->add($number->getIntegerPart(), '0');
        }

        if ($roundingMode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            if ($number->isNegative() === true) {
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
            if ($number->isNegative() === true) {
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

        throw new \InvalidArgumentException('Unknown rounding mode');
    }

    /**
     * @param $number
     *
     * @return string
     */
    private function roundDigit(Number $number)
    {
        if ($number->isCloserToNext()) {
            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        return $this->add($number->getIntegerPart(), '0');
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        return $this->floor($this->divide($this->multiply($amount, $ratio), $total));
    }
}
