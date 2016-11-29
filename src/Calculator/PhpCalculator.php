<?php

namespace Money\Calculator;

use Money\Calculator;
use Money\Money;
use Money\Number;

/**
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class PhpCalculator implements Calculator
{
    /**
     * {@inheritdoc}
     */
    public static function supported()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        return ($a < $b) ? -1 : (($a > $b) ? 1 : 0);
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        $amount = Number::fromString($amount);
        $addend = Number::fromString($addend);

        if ($amount->isInteger() && $addend->isInteger()) {
            $result = (string) $amount + (string) $addend;

            return (string) $result;
        }

        $integer = $amount->getIntegerPart() + $addend->getIntegerPart();
        if ($amount->isInteger()) {
            return $integer.'.'.$addend->getFractionalPart();
        }

        if ($addend->isInteger()) {
            return $integer.'.'.$amount->getFractionalPart();
        }

        $largestDigits = max(strlen($amount->getFractionalPart()), strlen($addend->getFractionalPart()));
        $basedAmount = $amount->getIntegerPart().str_pad($amount->getFractionalPart(), $largestDigits, '0');
        $basedAddend = $addend->getIntegerPart().str_pad($addend->getFractionalPart(), $largestDigits, '0');

        $basedResult = $basedAmount + $basedAddend;
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
            $result = (string) $amount - (string) $subtrahend;

            return (string) $result;
        }

        $largestDigits = max(strlen($amount->getFractionalPart()), strlen($subtrahend->getFractionalPart()));

        $basedAmount = $this->trimLeadingZeros(
            $amount->getIntegerPart().str_pad($amount->getFractionalPart(), $largestDigits, '0')
        );

        $basedSubtrahend = $this->trimLeadingZeros(
            $subtrahend->getIntegerPart().str_pad($subtrahend->getFractionalPart(), $largestDigits, '0')
        );

        $basedResult = $this->trimLeadingZeros($basedAmount - $basedSubtrahend);

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
        $result = $amount * $multiplier;

        $this->assertIntegerBounds($result);

        return (string) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        $result = $amount / $divisor;

        $this->assertIntegerBounds($result);

        return (string) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        return $this->castInteger(ceil($number));
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        return $this->castInteger(floor($number));
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number)
    {
        $result = ltrim($number, '-');

        $this->assertIntegerBounds($result);

        return (string) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $roundingMode)
    {
        if (Money::ROUND_HALF_POSITIVE_INFINITY === $roundingMode) {
            $number = Number::fromString((string) $number);

            if ($number->isHalf() === true) {
                return $this->castInteger(ceil((string) $number));
            }

            return $this->castInteger(round((string) $number, 0, Money::ROUND_HALF_UP));
        }

        if (Money::ROUND_HALF_NEGATIVE_INFINITY === $roundingMode) {
            $number = Number::fromString((string) $number);

            if ($number->isHalf() === true) {
                return $this->castInteger(floor((string) $number));
            }

            return $this->castInteger(round((string) $number, 0, Money::ROUND_HALF_DOWN));
        }

        return $this->castInteger(round($number, 0, $roundingMode));
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        return $this->castInteger(floor($amount * $ratio / $total));
    }

    /**
     * Asserts that an integer value didn't become something else
     * (after some arithmetic operation).
     *
     * @param int $amount
     *
     * @throws \OverflowException  If integer overflow occured
     * @throws \UnderflowException If integer underflow occured
     */
    private function assertIntegerBounds($amount)
    {
        if ($amount > PHP_INT_MAX) {
            throw new \OverflowException('You overflowed the maximum allowed integer (PHP_INT_MAX)');
        } elseif ($amount < ~PHP_INT_MAX) {
            throw new \UnderflowException('You underflowed the minimum allowed integer (PHP_INT_MAX)');
        }
    }

    /**
     * Casts an amount to integer ensuring that an overflow/underflow did not occur.
     *
     * @param int $amount
     *
     * @return string
     */
    private function castInteger($amount)
    {
        $this->assertIntegerBounds($amount);

        return (string) intval($amount);
    }
}
