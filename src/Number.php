<?php

namespace Money;

/**
 * Represents a numeric value.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
final class Number
{
    /**
     * @var string
     */
    private $number;

    /**
     * @var bool|int
     */
    private $decimalSeparatorPosition;

    /**
     * @param $number
     */
    public function __construct($number)
    {
        if (is_string($number) === false) {
            throw new \InvalidArgumentException(
                'Number expects a string for calculations'
            );
        }
        $this->number = (string) $number;
        $this->decimalSeparatorPosition = strpos($number, '.');
    }

    /**
     * @return bool
     */
    public function isDecimal()
    {
        return $this->decimalSeparatorPosition !== false;
    }

    /**
     * @return bool
     */
    public function isHalf()
    {
        if ($this->isDecimal() === false) {
            return false;
        }

        $firstDigitAfterDecimal = $this->number[$this->decimalSeparatorPosition + 1];

        if ($firstDigitAfterDecimal !== '5') {
            return false;
        }

        $decimalPositions = strlen(rtrim($this->number, '0')) - ($this->decimalSeparatorPosition + 1);
        if ($decimalPositions === 1) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCurrentEven()
    {
        if ($this->isDecimal() === false) {
            $firstDigitBeforeDecimal = $this->number[strlen($this->number) - 1];

            return $firstDigitBeforeDecimal % 2 === 0;
        }

        $firstDigitBeforeDecimal = $this->number[$this->decimalSeparatorPosition - 1];

        return $firstDigitBeforeDecimal % 2 === 0;
    }

    /**
     * @return bool
     */
    public function isCloserToNext()
    {
        if ($this->isDecimal() === false) {
            return false;
        }

        return $this->number[$this->decimalSeparatorPosition + 1] >= 5;
    }

    /**
     * Moves the decimal point.
     *
     * @param int $places Number of places to shift
     */
    public function movePoint($places)
    {
        if (!$places) {
            return;
        }

        $len = strlen($this->number);
        if (!$this->isDecimal()) {
            if ($places >= 0) {
                $this->number .= str_repeat('0', $places);
            } elseif ($len > abs($places)) {
                $pos = $len + $places;
                $this->number = substr($this->number, 0, $pos).'.'.substr($this->number, $pos);
                $this->decimalSeparatorPosition = $pos;
            } else {
                $this->number = '.'.str_pad($this->number, abs($places), '0', STR_PAD_LEFT);
                $this->decimalSeparatorPosition = 0;
            }
        } else {
            // Remove existing decimal point
            $this->number = substr($this->number, 0, $this->decimalSeparatorPosition).substr($this->number, $this->decimalSeparatorPosition + 1);
            $pos = $places + $this->decimalSeparatorPosition;
            $diff = $pos - ($len - 1);

            if ($pos <= 0) {
                $this->number = '.'.str_repeat('0', abs($pos)).$this->number;
                $this->decimalSeparatorPosition = 0;
            } elseif ($diff >= 0) {
                $this->number .= str_repeat('0', $diff);
                $this->decimalSeparatorPosition = false;
            } else {
                $this->number = substr($this->number, 0, $pos).'.'.substr($this->number, $pos);
                $this->decimalSeparatorPosition = $pos;
            }
        }

        // Fix the number
        if ($this->isDecimal()) {
            $this->number = trim($this->number, '0');
            if (!$this->decimalSeparatorPosition) {
                $this->number = '0'.$this->number;
                $this->decimalSeparatorPosition = 1;
            }

            $len = strlen($this->number);
            if (($len - 1) === $this->decimalSeparatorPosition) {
                $this->number = substr($this->number, 0, $len - 1);
                $this->decimalSeparatorPosition = false;
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->number;
    }

    /**
     * @param float $floatingPoint
     *
     * @return Number
     */
    public static function fromFloat($floatingPoint)
    {
        if (is_float($floatingPoint) === false) {
            throw new \InvalidArgumentException('Floating point expected');
        }

        return new self(sprintf('%.8g', $floatingPoint));
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return $this->number[0] === '-';
    }

    /**
     * @return string
     */
    public function getIntegerPart()
    {
        if ($this->decimalSeparatorPosition === false) {
            return $this->number;
        }

        return substr($this->number, 0, $this->decimalSeparatorPosition);
    }

    /**
     * @return string
     */
    public function getFractionalPart()
    {
        if ($this->decimalSeparatorPosition === false) {
            return '';
        }

        return rtrim(substr($this->number, $this->decimalSeparatorPosition + 1), '0');
    }

    /**
     * @return string
     */
    public function getIntegerRoundingMultiplier()
    {
        if ($this->isNegative()) {
            return '-1';
        }

        return '1';
    }

    /**
     * @param string|int $number
     *
     * @return bool
     */
    public static function isInteger($number)
    {
        // Check if number is a valid integer
        if (false !== filter_var($number, FILTER_VALIDATE_INT)) {
            return true;
        }

        // Check if number is invalid because of integer overflow
        $invalid = array_filter(
            str_split($number, strlen((string) PHP_INT_MAX) - 1),
            function ($chunk) {
                // Leading zeros should not invalidate the chunk
                $chunk = ltrim($chunk, '0');

                // Allow chunks containing zeros only
                return '' !== $chunk && false === filter_var($chunk, FILTER_VALIDATE_INT);
            }
        );

        return count($invalid) === 0;
    }

    /**
     * @param string $moneyValue
     * @param int    $targetDigits
     * @param int    $havingDigits
     *
     * @return string
     */
    public static function roundMoneyValue($moneyValue, $targetDigits, $havingDigits)
    {
        $valueLength = strlen($moneyValue);

        if ($targetDigits < $havingDigits && $moneyValue[$valueLength - $havingDigits + $targetDigits] >= 5) {
            $position = $valueLength - $havingDigits + $targetDigits;
            $addend = 1;

            while ($position > 0) {
                $newValue = (string) ((int) $moneyValue[$position - 1] + $addend);

                if ($newValue >= 10) {
                    $moneyValue[$position - 1] = $newValue[1];
                    $addend = $newValue[0];
                    --$position;
                    if ($position === 0) {
                        $moneyValue = $addend.$moneyValue;
                    }
                } else {
                    $moneyValue[$position - 1] = $newValue[0];
                    break;
                }
            }
        }

        return $moneyValue;
    }
}
