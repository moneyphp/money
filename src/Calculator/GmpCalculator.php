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
        $aNum = Number::fromNumber($a);
        $bNum = Number::fromNumber($b);

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
        $multiplier = Number::fromNumber($multiplier);

        if ($multiplier->isDecimal()) {
            list($multiplier, $decimalPlaces) = $this->ensureIntegerNumber($multiplier);

            $resultBase = gmp_strval(gmp_mul(gmp_init($amount), gmp_init((string) $multiplier)));

            if ('0' === $resultBase) {
                return '0';
            }

            $result = substr($resultBase, $decimalPlaces * -1);
            $resultLength = strlen($result);
            if ($decimalPlaces > $resultLength) {
                return '0.'.str_pad('', $decimalPlaces - $resultLength, '0').$result;
            }

            return substr($resultBase, 0, $decimalPlaces * -1).'.'.$result;
        }

        return gmp_strval(gmp_mul(gmp_init($amount), gmp_init((string) $multiplier)));
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        $divisor = Number::fromNumber($divisor);

        if ($divisor->isDecimal()) {
            list($divisor, $decimalPlaces) = $this->ensureIntegerNumber($divisor);

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

        if ($divisionOfRemainder[0] === '-') {
            $divisionOfRemainder = substr($divisionOfRemainder, 1);
        }

        return gmp_strval($integer).'.'.str_pad($divisionOfRemainder, $this->scale, '0', STR_PAD_LEFT);
    }

    /**
     * @return array
     */
    private function ensureIntegerNumber(Number $number)
    {
        $decimalPlaces = 0;

        if ($number->isDecimal()) {
            $decimalPlaces = strlen($number->getFractionalPart());
            $numberBase = $this->absolute($number->getIntegerPart());

            if ($numberBase) {
                $numberBase .= $number->getFractionalPart();
            } else {
                $numberBase = ltrim($number->getFractionalPart(), '0');
            }

            if ($number->isNegative()) {
                $numberBase = '-'.$numberBase;
            }

            $number = new Number($numberBase);
        }

        return [$number, $decimalPlaces];
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
            return (string) $number;
        }

        if ($number->isNegative()) {
            return $this->add($number->getIntegerPart(), '0');
        }

        return $this->add($number->getIntegerPart(), '1');
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
            return (string) $number;
        }

        if ($number->isNegative()) {
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
        $number = Number::fromNumber($number);

        if ($number->isInteger()) {
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
            if ($number->isCurrentEven()) {
                return $this->add($number->getIntegerPart(), '0');
            }

            return $this->add(
                $number->getIntegerPart(),
                $number->getIntegerRoundingMultiplier()
            );
        }

        if (Money::ROUND_HALF_ODD === $roundingMode) {
            if ($number->isCurrentEven()) {
                return $this->add(
                    $number->getIntegerPart(),
                    $number->getIntegerRoundingMultiplier()
                );
            }

            return $this->add($number->getIntegerPart(), '0');
        }

        if (Money::ROUND_HALF_POSITIVE_INFINITY === $roundingMode) {
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

        if (Money::ROUND_HALF_NEGATIVE_INFINITY === $roundingMode) {
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

    /**
     * {@inheritdoc}
     */
    public function mod($amount, $divisor)
    {
        // gmp_mod() only calculates non-negative integers, so we use absolutes
        $remainder = gmp_mod($this->absolute($amount), $this->absolute($divisor));

        // If the amount was negative, we negate the result of the modulus operation
        $amount = Number::fromNumber($amount);

        if ($amount->isNegative()) {
            $remainder = gmp_neg($remainder);
        }

        return gmp_strval($remainder);
    }

    /**
     * @test
     */
    public function it_divides_bug538()
    {
        $this->assertSame('-4.54545454545455', $this->getCalculator()->divide('-500', 110));
    }
}
