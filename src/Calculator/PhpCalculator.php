<?php
namespace Money\Calculator;

use Money\Calculator;

/**
 * Class NativePhpCalculator
 * @package Money\Calculator
 */
final class PhpCalculator implements Calculator
{
    /**
     * @return bool
     */
    public static function supported()
    {
        return true;
    }

    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    public function compare($a, $b)
    {
        return ($a < $b) ? -1 : (($a > $b) ? 1 : 0);
    }

    /**
     * @param int $amount
     * @param int $addend
     * @return int
     */
    public function add($amount, $addend)
    {
        $result = $amount + $addend;

        $this->assertInteger($result);

        return $result;
    }

    /**
     * @param $amount
     * @param $subtrahend
     * @return mixed
     */
    public function subtract($amount, $subtrahend)
    {
        $result = $amount - $subtrahend;

        $this->assertInteger($result);

        return $result;
    }

    /**
     * @param $amount
     * @param $multiplier
     * @return mixed
     */
    public function multiply($amount, $multiplier)
    {
        $result = $amount * $multiplier;

        $this->assertIntegerBounds($result);

        return $result;
    }

    /**
     * @param $amount
     * @param $divisor
     * @return mixed
     */
    public function divide($amount, $divisor)
    {
        $result = $amount / $divisor;

        $this->assertIntegerBounds($result);

        return $result;
    }

    /**
     * @param $number
     * @return float
     */
    public function ceil($number)
    {
        return $this->castInteger(ceil($number));
    }

    /**
     * @param $number
     * @return float
     */
    public function floor($number)
    {
        return $this->castInteger(floor($number));
    }

    /**
     * @param $number
     * @param $roundingMode
     * @return float
     */
    public function round($number, $roundingMode)
    {
        return $this->castInteger(round($number, 0, $roundingMode));
    }

    /**
     * @param $amount
     * @param $ratio
     * @param $total
     * @return int
     */
    public function share($amount, $ratio, $total)
    {
        return $this->castInteger(floor($amount * $ratio / $total));
    }

    /**
     * Asserts that an integer value didn't become something else
     * (after some arithmetic operation)
     *
     * @param numeric $amount
     *
     * @throws \OverflowException If integer overflow occured
     * @throws \UnderflowException If integer underflow occured
     */
    private function assertIntegerBounds($amount)
    {
        if ($amount > PHP_INT_MAX) {
            throw new \OverflowException;
        } elseif ($amount < ~PHP_INT_MAX) {
            throw new \UnderflowException;
        }
    }

    /**
     * Casts an amount to integer ensuring that an overflow/underflow did not occur
     *
     * @param numeric $amount
     *
     * @return int
     */
    private function castInteger($amount)
    {
        $this->assertIntegerBounds($amount);

        return intval($amount);
    }

    /**
     * Asserts that integer remains integer after arithmetic operations
     *
     * @param  numeric $amount
     */
    private function assertInteger($amount)
    {
        if (!is_int($amount)) {
            throw new \UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }

}
