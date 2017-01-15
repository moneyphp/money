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
        $a = Number::fromString($a);
        $b = Number::fromString($b);

        if ($a->isInteger() && $b->isInteger()) {
            return gmp_cmp((string) $a, (string) $b);
        }

        $compareIntegers = gmp_cmp($a->getIntegerPart(), $b->getIntegerPart());
        if ($compareIntegers !== '0') {
            return $compareIntegers;
        }

        return gmp_cmp($a->getFractionalPart(), $b->getFractionalPart());
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        $amount = Number::fromString($amount);
        $addend = Number::fromString($addend);

        if ($amount->isInteger() && $addend->isInteger()) {
            return gmp_strval(gmp_add((string) $amount, (string) $addend));
        }

        $integer = gmp_add($amount->getIntegerPart(), $addend->getIntegerPart());
        if ($amount->isInteger()) {
            return gmp_strval($integer).'.'.$addend->getFractionalPart();
        }

        if ($addend->isInteger()) {
            return gmp_strval($integer).'.'.$amount->getFractionalPart();
        }

        $largestDigits = max(strlen($amount->getFractionalPart()), strlen($addend->getFractionalPart()));
        $basedAmount = $amount->getIntegerPart().str_pad($amount->getFractionalPart(), $largestDigits, '0');
        $basedAddend = $addend->getIntegerPart().str_pad($addend->getFractionalPart(), $largestDigits, '0');

        $basedResult = gmp_strval(gmp_add($basedAmount, $basedAddend));
        $integerPart = substr($basedResult, 0, $largestDigits * -1);
        if ($integerPart === '-') {
            $integerPart = '-0';
        }

        return (string) (new Number(
            $integerPart,
            rtrim(substr($basedResult, $largestDigits * -1), '0')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function subtract($amount, $subtrahend)
    {
        $amount = Number::fromString($amount);
        $subtrahend = Number::fromString($subtrahend);

        if ($amount->isInteger() && $subtrahend->isInteger()) {
            return gmp_strval(gmp_sub((string) $amount, (string) $subtrahend));
        }

        $largestDigits = max(strlen($amount->getFractionalPart()), strlen($subtrahend->getFractionalPart()));

        $basedAmount = $this->trimLeadingZeros(
            $amount->getIntegerPart().str_pad($amount->getFractionalPart(), $largestDigits, '0')
        );

        $basedSubtrahend = $this->trimLeadingZeros(
            $subtrahend->getIntegerPart().str_pad($subtrahend->getFractionalPart(), $largestDigits, '0')
        );

        $basedResult = $this->trimLeadingZeros(gmp_strval(gmp_sub($basedAmount, $basedSubtrahend)));

        $leadingZeros = str_pad('', max(strlen($basedAmount), strlen($basedSubtrahend)), '0');
        if ($basedResult[0] === '-') {
            $basedResult = '-'.$leadingZeros.substr($basedResult, 1);
        } else {
            $basedResult = $leadingZeros.$basedResult;
        }

        $integerPart = $this->trimLeadingZeros(substr($basedResult, 0, $largestDigits * -1));
        if ($integerPart === '-') {
            $integerPart = '-0';
        }

        return (string) (new Number(
            $integerPart,
            rtrim(substr($basedResult, $largestDigits * -1), '0')
        ));
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function trimLeadingZeros($value)
    {
        if ($value[0] === '-') {
            return '-'.ltrim(substr($value, 1), '0');
        }

        return ltrim($value, '0');
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
