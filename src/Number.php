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
    private $integerPart;

    /**
     * @var string
     */
    private $fractionalPart;

    /**
     * @var array
     */
    private static $numbers = [0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1];

    /**
     * @var array
     */
    private static $signs = ['-' => 1];

    /**
     * @param string $integerPart
     * @param string $fractionalPart
     */
    public function __construct($integerPart, $fractionalPart = '')
    {
        if ($integerPart !== '' && $this->validateAsIntegerPart($integerPart) === false) {
            throw new \InvalidArgumentException(
                'Invalid number, integer part '.$integerPart.' is not an integer'
            );
        }

        if ($fractionalPart !== '' && $this->validateAsFractionalPart($fractionalPart) === false) {
            throw new \InvalidArgumentException(
                'Invalid number, fractional part '.$integerPart.' is not an integer'
            );
        }

        if ($integerPart === '-') {
            $integerPart = '-0';
        } else {
            $integerPart = ltrim($integerPart, '0');
        }

        $this->integerPart = $integerPart ? $integerPart : '0';
        $this->fractionalPart = $fractionalPart;
    }

    /**
     * @return bool
     */
    public function isDecimal()
    {
        return $this->fractionalPart !== '';
    }

    /**
     * @return bool
     */
    public function isInteger()
    {
        return $this->fractionalPart === '';
    }

    /**
     * @return bool
     */
    public function isHalf()
    {
        return $this->fractionalPart === '5';
    }

    /**
     * @return bool
     */
    public function isCurrentEven()
    {
        $lastIntegerPartNumber = $this->integerPart[strlen($this->integerPart) - 1];

        return $lastIntegerPartNumber % 2 === 0;
    }

    /**
     * @return bool
     */
    public function isCloserToNext()
    {
        if ($this->fractionalPart === '') {
            return false;
        }

        return $this->fractionalPart[0] >= 5;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->fractionalPart === '') {
            return $this->integerPart;
        }

        return $this->integerPart.'.'.$this->fractionalPart;
    }

    /**
     * @param $number
     *
     * @return self
     */
    public static function fromString($number)
    {
        $decimalSeparatorPosition = strpos($number, '.');
        if ($decimalSeparatorPosition === false) {
            return new self($number, '');
        }

        return new self(
            substr($number, 0, $decimalSeparatorPosition),
            rtrim(substr($number, $decimalSeparatorPosition + 1), '0')
        );
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

        return self::fromString(sprintf('%.8g', $floatingPoint));
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return $this->integerPart[0] === '-';
    }

    /**
     * @return string
     */
    public function getIntegerPart()
    {
        return $this->integerPart;
    }

    /**
     * @return string
     */
    public function getFractionalPart()
    {
        return $this->fractionalPart;
    }

    /**
     * @return string
     */
    public function getIntegerRoundingMultiplier()
    {
        if ($this->integerPart[0] === '-') {
            return '-1';
        }

        return '1';
    }

    /**
     * @param string $number
     *
     * @return bool
     */
    private static function validateAsIntegerPart($number)
    {
        $position = 0;
        while (isset($number[$position])) {
            $digit = $number[$position];
            if (!isset(static::$numbers[$digit]) && !($position === 0 && isset(static::$signs[$digit]))) {
                return false;
            }

            ++$position;
        }

        return true;
    }

    /**
     * @param string $number
     *
     * @return bool
     */
    private static function validateAsFractionalPart($number)
    {
        $position = 0;
        while (isset($number[$position])) {
            $digit = $number[$position];
            if (!isset(static::$numbers[$digit])) {
                return false;
            }

            ++$position;
        }

        return true;
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
