<?php

namespace Money\Calculator;

use Decimal\Decimal;
use Money\Calculator;
use Money\Money;
use Money\Number;

/**
 * @author Rudi Theunissen <rtheunissen@php.net>
 */
final class DecimalCalculator implements Calculator
{
    /**
     * @var int
     */
    private $scale;

    /**
     * @var int The maximum number of digits to the left of the decimal point.
     *          This should be plenty, but can be increased at very little cost.
     */
    const MAX_INTEGRAL_DIGITS = 36;

    /**
     * @var int The number of digits to the right of the decimal point
     */
    const DEFAULT_SCALE = 14;

    /**
     * @var array A partial map of rounding modes
     */
    const ROUNDING_MODES = [
        Money::ROUND_HALF_UP => Decimal::ROUND_HALF_UP,
        Money::ROUND_HALF_DOWN => Decimal::ROUND_HALF_DOWN,
        Money::ROUND_HALF_EVEN => Decimal::ROUND_HALF_EVEN,
        Money::ROUND_UP => Decimal::ROUND_UP,
        Money::ROUND_DOWN => Decimal::ROUND_DOWN,
        Money::ROUND_HALF_ODD => Decimal::ROUND_HALF_ODD,
    ];

    /**
     * @param int $scale
     */
    public function __construct($scale = self::DEFAULT_SCALE)
    {
        $this->scale = $scale;
    }

    /**
     * {@inheritdoc}
     */
    public static function supported()
    {
        return extension_loaded('decimal');
    }

    /**
     * Internal helper to centralize conversion to decimal.
     */
    private function asDecimal($value)
    {
        if (is_float($value)) {
            $value = number_format($value, $this->scale, '.', '');
        } else {
            $value = (string) $value;
        }

        return new Decimal($value, self::MAX_INTEGRAL_DIGITS + $this->scale);
    }

    /**
     * Internal helper to centralize conversion from decimal to string.
     */
    private function toString(Decimal $value)
    {
        $result = $value->toFixed($this->scale, false, Decimal::ROUND_HALF_UP);
        $result = rtrim($result, '0');
        $result = rtrim($result, '.');

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($a, $b)
    {
        $a = $this->asDecimal($a);
        $b = $this->asDecimal($b);

        return $a->compareTo($b);
    }

    /**
     * {@inheritdoc}
     */
    public function add($amount, $addend)
    {
        return $this->toString($this->asDecimal($amount) + $this->asDecimal($addend));
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
        return $this->toString($this->asDecimal($amount) - $this->asDecimal($subtrahend));
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($amount, $multiplier)
    {
        return $this->toString($this->asDecimal($amount) * $this->asDecimal($multiplier));
    }

    /**
     * {@inheritdoc}
     */
    public function divide($amount, $divisor)
    {
        return $this->toString($this->asDecimal($amount) / $this->asDecimal($divisor));
    }

    /**
     * {@inheritdoc}
     */
    public function share($amount, $ratio, $total)
    {
        $decimal = $this->asDecimal($amount) * $this->asDecimal($ratio) / $this->asDecimal($total);

        return $this->toString($decimal->floor());
    }

    /**
     * {@inheritdoc}
     */
    public function mod($amount, $divisor)
    {
        return $this->toString($this->asDecimal($amount) % $this->asDecimal($divisor));
    }

    /**
     * {@inheritdoc}
     */
    public function ceil($number)
    {
        return $this->toString($this->asDecimal($number)->ceil());
    }

    /**
     * {@inheritdoc}
     */
    public function floor($number)
    {
        return $this->toString($this->asDecimal($number)->floor());
    }

    /**
     * {@inheritdoc}
     */
    public function absolute($number)
    {
        return ltrim($number, '-');
    }

    /**
     * @return int The Decimal rounding mode that matches the Money mode
     */
    private function getDecimalRoundingMode(Decimal $decimal, $mode)
    {
        $modes = self::ROUNDING_MODES;

        if (array_key_exists($mode, $modes)) {
            return $modes[$mode];
        }

        if ($mode === Money::ROUND_HALF_POSITIVE_INFINITY) {
            return $decimal->isNegative()
                ? Decimal::ROUND_HALF_DOWN
                : Decimal::ROUND_HALF_UP;
        }

        if ($mode === Money::ROUND_HALF_NEGATIVE_INFINITY) {
            return $decimal->isPositive()
                ? Decimal::ROUND_HALF_DOWN
                : Decimal::ROUND_HALF_UP;
        }

        /* This should never happen. */
        throw new \InvalidArgumentException('Unknown rounding mode');
    }

    /**
     * {@inheritdoc}
     */
    public function round($number, $mode)
    {
        $decimal = $this->asDecimal($number);

        if ($decimal->isInteger()) {
            return $this->toString($decimal);
        }

        /* Map the rounding mode to Decimal's constants. */
        $mode = $this->getDecimalRoundingMode($decimal, $mode);

        return $this->toString($decimal->round(0, $mode));
    }
}
