<?php

/**
 * This file is part of the Money library.
 *
 * Copyright (c) 2011-2014 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use InvalidArgumentException;
use JsonSerializable;
use OverflowException;
use UnderflowException;
use UnexpectedValueException;

/**
 * Money Value Object
 *
 * @author Mathias Verraes
 *
 * @method static Money AED(int $amount)
 * @method static Money AFN(int $amount)
 * @method static Money ALL(int $amount)
 * @method static Money AMD(int $amount)
 * @method static Money ANG(int $amount)
 * @method static Money AOA(int $amount)
 * @method static Money ARS(int $amount)
 * @method static Money AUD(int $amount)
 * @method static Money AWG(int $amount)
 * @method static Money AZN(int $amount)
 * @method static Money BAM(int $amount)
 * @method static Money BBD(int $amount)
 * @method static Money BDT(int $amount)
 * @method static Money BGN(int $amount)
 * @method static Money BHD(int $amount)
 * @method static Money BIF(int $amount)
 * @method static Money BMD(int $amount)
 * @method static Money BND(int $amount)
 * @method static Money BOB(int $amount)
 * @method static Money BRL(int $amount)
 * @method static Money BSD(int $amount)
 * @method static Money BTN(int $amount)
 * @method static Money BWP(int $amount)
 * @method static Money BYR(int $amount)
 * @method static Money BZD(int $amount)
 * @method static Money CAD(int $amount)
 * @method static Money CDF(int $amount)
 * @method static Money CHF(int $amount)
 * @method static Money CLF(int $amount)
 * @method static Money CLP(int $amount)
 * @method static Money CNY(int $amount)
 * @method static Money COP(int $amount)
 * @method static Money CRC(int $amount)
 * @method static Money CUP(int $amount)
 * @method static Money CVE(int $amount)
 * @method static Money CZK(int $amount)
 * @method static Money DJF(int $amount)
 * @method static Money DKK(int $amount)
 * @method static Money DOP(int $amount)
 * @method static Money DZD(int $amount)
 * @method static Money EEK(int $amount)
 * @method static Money EGP(int $amount)
 * @method static Money ETB(int $amount)
 * @method static Money EUR(int $amount)
 * @method static Money FJD(int $amount)
 * @method static Money FKP(int $amount)
 * @method static Money GBP(int $amount)
 * @method static Money GEL(int $amount)
 * @method static Money GHS(int $amount)
 * @method static Money GIP(int $amount)
 * @method static Money GMD(int $amount)
 * @method static Money GNF(int $amount)
 * @method static Money GTQ(int $amount)
 * @method static Money GYD(int $amount)
 * @method static Money HKD(int $amount)
 * @method static Money HNL(int $amount)
 * @method static Money HRK(int $amount)
 * @method static Money HTG(int $amount)
 * @method static Money HUF(int $amount)
 * @method static Money IDR(int $amount)
 * @method static Money ILS(int $amount)
 * @method static Money INR(int $amount)
 * @method static Money IQD(int $amount)
 * @method static Money IRR(int $amount)
 * @method static Money ISK(int $amount)
 * @method static Money JEP(int $amount)
 * @method static Money JMD(int $amount)
 * @method static Money JOD(int $amount)
 * @method static Money JPY(int $amount)
 * @method static Money KES(int $amount)
 * @method static Money KGS(int $amount)
 * @method static Money KHR(int $amount)
 * @method static Money KMF(int $amount)
 * @method static Money KPW(int $amount)
 * @method static Money KRW(int $amount)
 * @method static Money KWD(int $amount)
 * @method static Money KYD(int $amount)
 * @method static Money KZT(int $amount)
 * @method static Money LAK(int $amount)
 * @method static Money LBP(int $amount)
 * @method static Money LKR(int $amount)
 * @method static Money LRD(int $amount)
 * @method static Money LSL(int $amount)
 * @method static Money LTL(int $amount)
 * @method static Money LVL(int $amount)
 * @method static Money LYD(int $amount)
 * @method static Money MAD(int $amount)
 * @method static Money MDL(int $amount)
 * @method static Money MGA(int $amount)
 * @method static Money MKD(int $amount)
 * @method static Money MMK(int $amount)
 * @method static Money MNT(int $amount)
 * @method static Money MOP(int $amount)
 * @method static Money MRO(int $amount)
 * @method static Money MUR(int $amount)
 * @method static Money MVR(int $amount)
 * @method static Money MWK(int $amount)
 * @method static Money MXN(int $amount)
 * @method static Money MYR(int $amount)
 * @method static Money MZN(int $amount)
 * @method static Money NAD(int $amount)
 * @method static Money NGN(int $amount)
 * @method static Money NIO(int $amount)
 * @method static Money NOK(int $amount)
 * @method static Money NPR(int $amount)
 * @method static Money NZD(int $amount)
 * @method static Money OMR(int $amount)
 * @method static Money PAB(int $amount)
 * @method static Money PEN(int $amount)
 * @method static Money PGK(int $amount)
 * @method static Money PHP(int $amount)
 * @method static Money PKR(int $amount)
 * @method static Money PLN(int $amount)
 * @method static Money PYG(int $amount)
 * @method static Money QAR(int $amount)
 * @method static Money RON(int $amount)
 * @method static Money RSD(int $amount)
 * @method static Money RUB(int $amount)
 * @method static Money RWF(int $amount)
 * @method static Money SAR(int $amount)
 * @method static Money SBD(int $amount)
 * @method static Money SCR(int $amount)
 * @method static Money SDG(int $amount)
 * @method static Money SEK(int $amount)
 * @method static Money SGD(int $amount)
 * @method static Money SHP(int $amount)
 * @method static Money SLL(int $amount)
 * @method static Money SOS(int $amount)
 * @method static Money SRD(int $amount)
 * @method static Money STD(int $amount)
 * @method static Money SVC(int $amount)
 * @method static Money SYP(int $amount)
 * @method static Money SZL(int $amount)
 * @method static Money THB(int $amount)
 * @method static Money TJS(int $amount)
 * @method static Money TMT(int $amount)
 * @method static Money TND(int $amount)
 * @method static Money TOP(int $amount)
 * @method static Money TRY(int $amount)
 * @method static Money TTD(int $amount)
 * @method static Money TWD(int $amount)
 * @method static Money TZS(int $amount)
 * @method static Money UAH(int $amount)
 * @method static Money UGX(int $amount)
 * @method static Money USD(int $amount)
 * @method static Money UYU(int $amount)
 * @method static Money UZS(int $amount)
 * @method static Money VEF(int $amount)
 * @method static Money VND(int $amount)
 * @method static Money VUV(int $amount)
 * @method static Money WST(int $amount)
 * @method static Money XAF(int $amount)
 * @method static Money XCD(int $amount)
 * @method static Money XDR(int $amount)
 * @method static Money XOF(int $amount)
 * @method static Money XPF(int $amount)
 * @method static Money YER(int $amount)
 * @method static Money ZAR(int $amount)
 * @method static Money ZMK(int $amount)
 * @method static Money ZWL(int $amount)
 */
final class Money implements JsonSerializable
{
    const ROUND_HALF_UP   = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD  = PHP_ROUND_HALF_ODD;
    const ROUND_UP  = 5;
    const ROUND_DOWN  = 6;

    /**
     * Internal value
     *
     * @var int
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @param int  $amount   Amount, expressed in the smallest units of $currency (eg cents)
     * @param Currency $currency
     *
     * @throws InvalidArgumentException If amount is not integer
     */
    public function __construct($amount, Currency $currency)
    {
        if (!is_int($amount)) {
            throw new InvalidArgumentException('Amount must be an integer');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Convenience factory method for a Money object
     *
     * <code>
     * $fiveDollar = Money::USD(500);
     * </code>
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Money
     */
    public static function __callStatic($method, $arguments)
    {
        return new Money($arguments[0], new Currency($method));
    }

    /**
     * Returns a new Money instance based on the current one using the Currency
     *
     * @param int $amount
     *
     * @return Money
     */
    private function newInstance($amount)
    {
        return new Money($amount, $this->currency);
    }

    /**
     * Checks whether a Money has the same Currency as this
     *
     * @param Money $other
     *
     * @return boolean
     */
    public function isSameCurrency(Money $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * Asserts that a Money has the same currency as this
     *
     * @throws InvalidArgumentException If $other has a different currency
     */
    private function assertSameCurrency(Money $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Currencies must be identical');
        }
    }

    /**
     * Checks whether the value represented by this object equals to the other
     *
     * @param Money $other
     *
     * @return boolean
     */
    public function equals(Money $other)
    {
        return $this->isSameCurrency($other) && $this->amount == $other->amount;
    }

    /**
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this object is considered to be respectively
     * less than, equal to, or greater than the other
     *
     * @param Money $other
     *
     * @return int
     */
    public function compare(Money $other)
    {
        $this->assertSameCurrency($other);

        if ($this->amount < $other->amount) {
            return -1;
        } elseif ($this->amount == $other->amount) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Checks whether the value represented by this object is greater than the other
     *
     * @param Money $other
     *
     * @return boolean
     */
    public function greaterThan(Money $other)
    {
        return 1 == $this->compare($other);
    }

    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function greaterThanOrEqual(Money $other)
    {
        return 0 >= $this->compare($other);
    }
    
    /**
     * Checks whether the value represented by this object is less than the other
     *
     * @param Money $other
     *
     * @return boolean
     */
    public function lessThan(Money $other)
    {
        return -1 == $this->compare($other);
    }
    
    /**
     * @param \Money\Money $other
     * @return bool
     */
    public function lessThanOrEqual(Money $other)
    {
        return 0 <= $this->compare($other);
    }

    /**
     * Returns the value represented by this object
     *
     * @deprecated Use getAmount() instead
     *
     * @return int
     */
    public function getUnits()
    {
        return $this->amount;
    }

    /**
     * Returns the value represented by this object
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the currency of this object
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Asserts that integer remains integer after arithmetic operations
     *
     * @param  numeric $amount
     */
    private function assertInteger($amount)
    {
        if (!is_int($amount)) {
            throw new UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }

    /**
     * Returns a new Money object that represents
     * the sum of this and an other Money object
     *
     * @param Money $addend
     *
     * @return Money
     */
    public function add(Money $addend)
    {
        $this->assertSameCurrency($addend);

        $amount = $this->amount + $addend->amount;

        $this->assertInteger($amount);

        return $this->newInstance($amount);
    }

    /**
     * Returns a new Money object that represents
     * the difference of this and an other Money object
     *
     * @param Money $subtrahend
     *
     * @return Money
     */
    public function subtract(Money $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        $amount = $this->amount - $subtrahend->amount;

        $this->assertInteger($amount);

        return $this->newInstance($amount);
    }

    /**
     * Asserts that the operand is integer or float
     *
     * @throws InvalidArgumentException If $operand is neither integer nor float
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand should be an integer or a float');
        }
    }

    /**
     * Asserts that an integer value didn't become something else
     * (after some arithmetic operation)
     *
     * @param numeric $amount
     *
     * @throws OverflowException If integer overflow occured
     * @throws UnderflowException If integer underflow occured
     */
    private function assertIntegerBounds($amount)
    {
        if ($amount > PHP_INT_MAX) {
            throw new OverflowException;
        } elseif ($amount < ~PHP_INT_MAX) {
            throw new UnderflowException;
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
     * Asserts that rounding mode is a valid integer value
     *
     * @param int $roundingMode
     *
     * @throws InvalidArgumentException If $roundingMode is not valid
     */
    private function assertRoundingMode($roundingMode)
    {
        if (!in_array(
            $roundingMode, [
                self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD,
                self::ROUND_HALF_UP, self::ROUND_UP, self::ROUND_DOWN
            ]
        )) {
            throw new InvalidArgumentException(
                'Rounding mode should be Money::ROUND_HALF_DOWN | ' .
                'Money::ROUND_HALF_EVEN | Money::ROUND_HALF_ODD | ' .
                'Money::ROUND_HALF_UP | Money::ROUND_UP | Money::ROUND_DOWN'
            );
        }
    }

    /**
     * Returns a new Money object that represents
     * the multiplied value by the given factor
     *
     * @param numeric $multiplier
     * @param int $roundingMode
     *
     * @return Money
     */
    public function multiply($multiplier, $roundingMode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);

        $this->assertRoundingMode($roundingMode);

        $product = $this->round($this->amount * $multiplier, $roundingMode);

        return $this->newInstance($product);
    }

    /**
     * @param Currency $targetCurrency
     * @param float|int $conversionRate
     * @param int $roundingMode
     * @return Money
     */
    public function convert(Currency $targetCurrency, $conversionRate, $roundingMode = Money::ROUND_HALF_UP)
    {
        $this->assertRoundingMode($roundingMode);
        $amount = round($this->amount * $conversionRate, 0, $roundingMode);
        $amount = $this->castInteger($amount);
        return new Money($amount, $targetCurrency);
    }

    /**
     * Returns a new Money object that represents
     * the divided value by the given factor
     *
     * @param numeric $divisor
     * @param int $roundingMode
     *
     * @return Money
     */
    public function divide($divisor, $roundingMode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($divisor);
        $this->assertRoundingMode($roundingMode);

        if ($divisor === 0 || $divisor === 0.0) {
            throw new InvalidArgumentException('Division by zero');
        }

        $quotient = $this->round($this->amount / $divisor, $roundingMode);

        return $this->newInstance($quotient);
    }

    /**
     * Allocate the money according to a list of ratios
     *
     * @param array $ratios
     *
     * @return Money[]
     */
    public function allocate(array $ratios)
    {
        $remainder = $this->amount;
        $results = array();
        $total = array_sum($ratios);

        foreach ($ratios as $ratio) {
            $share = $this->castInteger($this->amount * $ratio / $total);
            $results[] = $this->newInstance($share);
            $remainder -= $share;
        }

        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->amount++;
            $remainder--;
        }

        return $results;
    }

    /**
     * Allocate the money among N targets
     *
     * @param int $n
     *
     * @return Money[]
     *
     * @throws InvalidArgumentException If number of targets is not an integer
     */
    public function allocateTo($n)
    {
        if (!is_int($n)) {
            throw new InvalidArgumentException('Number of targets must be an integer');
        }

        $amount = intval($this->amount / $n);
        $results = array();

        for ($i = 0; $i < $n; $i++) {
            $results[$i] = $this->newInstance($amount);
        }

        for ($i = 0; $i < $this->amount % $n; $i++) {
            $results[$i]->amount++;
        }

        return $results;
    }

    /**
     * @param int|float $amount
     * @param $rounding_mode
     * @return int
     */
    private function round($amount, $rounding_mode)
    {
        $this->assertRoundingMode($rounding_mode);
        if ($rounding_mode === self::ROUND_UP) {
            return $this->castInteger(ceil($amount));
        }
        if ($rounding_mode === self::ROUND_DOWN) {
            return $this->castInteger(floor($amount));
        }

        return $this->castInteger(round($amount, 0, $rounding_mode));
    }

    /**
     * Checks if the value represented by this object is zero
     *
     * @return boolean
     */
    public function isZero()
    {
        return $this->amount === 0;
    }

    /**
     * Checks if the value represented by this object is positive
     *
     * @return boolean
     */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /**
     * Checks if the value represented by this object is negative
     *
     * @return boolean
     */
    public function isNegative()
    {
        return $this->amount < 0;
    }

    /**
     * Creates units from string
     *
     * @param string $string
     *
     * @return int
     *
     * @throws InvalidArgumentException If $string cannot be parsed
     */
    public static function stringToUnits($string)
    {
        $sign = "(?P<sign>[-\+])?";
        $digits = "(?P<digits>\d*)";
        $separator = "(?P<separator>[.,])?";
        $decimals = "(?P<decimal1>\d)?(?P<decimal2>\d)?";
        $pattern = "/^".$sign.$digits.$separator.$decimals."$/";

        if (!preg_match($pattern, trim($string), $matches)) {
            throw new InvalidArgumentException("The value could not be parsed as money");
        }

        $units = $matches['sign'] == "-" ? "-" : "";
        $units .= $matches['digits'];
        $units .= isset($matches['decimal1']) ? $matches['decimal1'] : "0";
        $units .= isset($matches['decimal2']) ? $matches['decimal2'] : "0";

        return (int) $units;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'amount' => $this->amount,
            'currency' => $this->currency,
        );
    }
}
