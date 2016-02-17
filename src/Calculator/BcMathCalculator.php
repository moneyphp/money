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
        return bccomp($a, $b);
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
        $decimalSeparatorPosition = strpos($number, '.');
        if ($decimalSeparatorPosition === false) {
            return $number;
        }

        return bcadd($number, '1', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        return bcadd($number, '0', 0);
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

        if ($roundingMode === Money::ROUND_HALF_DOWN) {
            return $this->floor((string) $number);
        }

        if ($roundingMode === Money::ROUND_HALF_UP) {
            return $this->ceil((string) $number);
        }

        if ($roundingMode === Money::ROUND_HALF_EVEN) {
            if ($number->isCurrentEven() === true) {
                return $this->floor((string) $number);
            } else {
                return $this->ceil((string) $number);
            }
        }

        if ($roundingMode === Money::ROUND_HALF_ODD) {
            if ($number->isCurrentEven() === true) {
                return $this->ceil((string) $number);
            } else {
                return $this->floor((string) $number);
            }
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
            return $this->ceil((string) $number);
        }

        return $this->floor((string) $number);
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        return $this->floor(bcdiv(bcmul($amount, $ratio, $this->scale), $total, $this->scale));
    }
}
