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
}
