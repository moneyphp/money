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
        return extension_loaded('gmp');
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        $aNum = Number::fromString((string) $a);
        $bNum = Number::fromString((string) $b);

        if ($aNum->isDecimal() || $bNum->isDecimal()) {
            $integersCompared = gmp_cmp($aNum->getIntegerPart(), $bNum->getIntegerPart());
            if ($integersCompared !== 0) {
                return $integersCompared;
            }

            return gmp_cmp($aNum->getFractionalPart(), $bNum->getFractionalPart());
        }

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
        $multiplier = Number::fromString((string) $multiplier);

        if ($multiplier->isDecimal()) {
            $decimalPlaces = strlen($multiplier->getFractionalPart());
            $multiplierBase = $multiplier->getIntegerPart();

            if ($multiplierBase) {
                $multiplierBase .= $multiplier->getFractionalPart();
            } else {
                $multiplierBase = ltrim($multiplier->getFractionalPart(), '0');
            }

            $resultBase = gmp_strval(gmp_mul(gmp_init($amount), gmp_init($multiplierBase)));

            if ('0' === $resultBase) {
                return '0';
            }

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
        $divisor = Number::fromString((string) $divisor);

        if ($divisor->isDecimal()) {
            $decimalPlaces = strlen($divisor->getFractionalPart());

            if ($divisor->getIntegerPart()) {
                $divisor = Number::fromString(
                    $divisor->getIntegerPart().$divisor->getFractionalPart()
                );
            } else {
                $divisor = Number::fromString(ltrim($divisor->getFractionalPart(), '0'));
            }

            $amount = gmp_strval(gmp_mul(gmp_init($amount), gmp_init('1'.str_pad('', $decimalPlaces, '0'))));
        }

        list($integer, $remainder) = gmp_div_qr(gmp_init($amount), gmp_init((string) $divisor));

        if (gmp_cmp($remainder, '0') === 0) {
            return gmp_strval($integer);
        }

        $divisionOfRemainder = gmp_strval(
            gmp_div_q(
                gmp_mul($remainder, gmp_init('1'.str_pad('', $this->scale, '0'))),
                gmp_init((string) $divisor),
                GMP_ROUND_MINUSINF
            )
        );

        return gmp_strval($integer).'.'.str_pad($divisionOfRemainder, $this->scale, '0', STR_PAD_LEFT);
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
            return $this->add($number->getIntegerPart(), '0');
        }

        return $this->add($number->getIntegerPart(), '1');
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
            return $this->add($number->getIntegerPart(), '-1');
        }

        return $this->add($number->getIntegerPart(), '0');
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
            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if (Money::ROUND_HALF_DOWN === $roundingMode) {
            return $this->add($number->getIntegerPart(), '0');
        }

        if (Money::ROUND_HALF_EVEN === $roundingMode) {
            if ($number->isCurrentEven() === true) {
                return $this->add($number->getIntegerPart(), '0');
            }

            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if (Money::ROUND_HALF_ODD === $roundingMode) {
            if ($number->isCurrentEven() === true) {
                return $this->add(
                    $number->getIntegerPart(),
                    $number->getIntegerRoundingMultiplier()
                );
            }

            return $this->add($number->getIntegerPart(), '0');
        }

        if (Money::ROUND_HALF_POSITIVE_INFINITY === $roundingMode) {
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

        if (Money::ROUND_HALF_NEGATIVE_INFINITY === $roundingMode) {
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
