<?php

namespace Money\Calculator;

use Money\Calculator;
use Money\Money;
use Money\Number;

/**
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class BcMathCalculator implements Calculator
{
    /**
     * @var string
     */
    private $scale;

    /**
     * @param int $scale
     */
    public function __construct($scale = 14)
    {
        $this->scale = $scale;
    }

    /**
     * {@inheritdoc}
     */
    public static function supported()
    {
        return extension_loaded('bcmath');
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        return bccomp($a, $b, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        return bcadd($amount, $addend, 0);
    }

    /**
     * {@inheritdoc}
     *
     * @param $amount
     * @param $subtrahend
     *
     * @return string
     */
    public function subtract($amount, $subtrahend)
    {
        return bcsub($amount, $subtrahend, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier)
    {
        return bcmul($amount, $multiplier, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        return bcdiv($amount, $divisor, $this->scale);
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        $number = Number::fromString((string) $number);

        if ($number->isDecimal() === false) {
            return (string) $number;
        }

        if ($number->isNegative() === true) {
            return bcadd((string) $number, '0', 0);
        }

        return bcadd((string) $number, '1', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        $number = Number::fromString((string) $number);

        if ($number->isDecimal() === false) {
            return (string) $number;
        }

        if ($number->isNegative() === true) {
            return bcadd((string) $number, '-1', 0);
        }

        return bcadd($number, '0', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number)
    {
        return ltrim($number, '-');
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $roundingMode)
    {
        $number = Number::fromString((string) $number);

        if ($number->isDecimal() === false) {
            return (string) $number;
        }

        if ($number->isHalf() === false) {
            return $this->roundDigit($number);
        }

        if (Money::ROUND_HALF_UP === $roundingMode) {
            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if (Money::ROUND_HALF_DOWN === $roundingMode) {
            return bcadd((string) $number, '0', 0);
        }

        if (Money::ROUND_HALF_EVEN === $roundingMode) {
            if ($number->isCurrentEven() === true) {
                return bcadd((string) $number, '0', 0);
            }

            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if (Money::ROUND_HALF_ODD === $roundingMode) {
            if ($number->isCurrentEven() === true) {
                return bcadd(
                    (string) $number,
                    $number->getIntegerRoundingMultiplier(),
                    0
                );
            }

            return bcadd((string) $number, '0', 0);
        }

        if (Money::ROUND_HALF_POSITIVE_INFINITY === $roundingMode) {
            if ($number->isNegative() === true) {
                return bcadd((string) $number, '0', 0);
            }

            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        if (Money::ROUND_HALF_NEGATIVE_INFINITY === $roundingMode) {
            if ($number->isNegative() === true) {
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
            return bcadd(
                (string) $number,
                $number->getIntegerRoundingMultiplier(),
                0
            );
        }

        return bcadd((string) $number, '0', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        return $this->floor(bcdiv(bcmul($amount, $ratio, $this->scale), $total, $this->scale));
    }

    /**
     * {@inheritdoc}
     */
    public function mod($amount, $divisor)
    {
        return bcmod($amount, $divisor);
    }
}
