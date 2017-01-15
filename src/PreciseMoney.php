<?php

namespace Money;

use Money\Calculator\BcMathCalculator;
use Money\Calculator\GmpCalculator;

/**
 * PreciseMoney Value Object.
 *
 * @author Frederik Bosch
 */
final class PreciseMoney implements \JsonSerializable
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;
    const ROUND_UP = 5;
    const ROUND_DOWN = 6;
    const ROUND_HALF_POSITIVE_INFINITY = 7;
    const ROUND_HALF_NEGATIVE_INFINITY = 8;

    /**
     * Internal value.
     *
     * @var string
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var Calculator
     */
    private static $calculator;

    /**
     * @var array
     */
    private static $calculators = [
        BcMathCalculator::class,
        GmpCalculator::class,
    ];

    /**
     * @param string   $amount   Amount, expressed in the smallest units of $currency (eg cents)
     * @param Currency $currency
     *
     * @throws \InvalidArgumentException If amount is not integer
     */
    public function __construct($amount, Currency $currency)
    {
        if (!is_int($amount) && !is_string($amount)) {
            throw new \InvalidArgumentException('Amount must be a string');
        }

        $this->amount = (string) Number::fromString((string) $amount);
        $this->currency = $currency;
    }

    /**
     * Convenience factory method for a Money object.
     *
     * <code>
     * $fiveDollar = Money::USD(500);
     * </code>
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Money
     *
     * @throws \InvalidArgumentException If amount is not integer
     */
    public static function __callStatic($method, $arguments)
    {
        return new self($arguments[0], new Currency($method));
    }

    /**
     * Returns a new Money instance based on the current one using the Currency.
     *
     * @param string $amount
     *
     * @return Money
     *
     * @throws \InvalidArgumentException If amount is not integer
     */
    private function newInstance($amount)
    {
        return new self($amount, $this->currency);
    }

    /**
     * Checks whether a Money has the same Currency as this.
     *
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function isSameCurrency(PreciseMoney $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * Asserts that a Money has the same currency as this.
     *
     * @param PreciseMoney $other
     *
     * @throws \InvalidArgumentException If $other has a different currency
     */
    private function assertSameCurrency(PreciseMoney $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currencies must be identical');
        }
    }

    /**
     * Checks whether the value represented by this object equals to the other.
     *
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function equals(PreciseMoney $other)
    {
        return $this->isSameCurrency($other) && $this->amount === $other->amount;
    }

    /**
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this object is considered to be respectively
     * less than, equal to, or greater than the other.
     *
     * @param PreciseMoney $other
     *
     * @return int
     */
    public function compare(PreciseMoney $other)
    {
        $this->assertSameCurrency($other);

        return $this->getCalculator()->compare($this->amount, $other->amount);
    }

    /**
     * Checks whether the value represented by this object is greater than the other.
     *
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function greaterThan(PreciseMoney $other)
    {
        return $this->compare($other) === 1;
    }

    /**
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function greaterThanOrEqual(PreciseMoney $other)
    {
        return $this->compare($other) >= 0;
    }

    /**
     * Checks whether the value represented by this object is less than the other.
     *
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function lessThan(PreciseMoney $other)
    {
        return $this->compare($other) === -1;
    }

    /**
     * @param PreciseMoney $other
     *
     * @return bool
     */
    public function lessThanOrEqual(PreciseMoney $other)
    {
        return $this->compare($other) <= 0;
    }

    /**
     * Returns the value represented by this object.
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the currency of this object.
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Returns a new Money object that represents
     * the sum of this and an other Money object.
     *
     * @param PreciseMoney $addend
     *
     * @return Money
     */
    public function add(PreciseMoney $addend)
    {
        $this->assertSameCurrency($addend);

        return new self($this->getCalculator()->add($this->amount, $addend->amount), $this->currency);
    }

    /**
     * Returns a new Money object that represents
     * the difference of this and an other Money object.
     *
     * @param PreciseMoney $subtrahend
     *
     * @return PreciseMoney
     */
    public function subtract(PreciseMoney $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->getCalculator()->subtract($this->amount, $subtrahend->amount), $this->currency);
    }

    /**
     * Asserts that the operand is integer or float.
     *
     * @param float|int|string $operand
     *
     * @throws \InvalidArgumentException If $operand is neither integer nor float
     */
    private function assertOperand($operand)
    {
        if (!is_numeric($operand)) {
            throw new \InvalidArgumentException(sprintf(
                'Operand should be a numeric value, "%s" given.',
                is_object($operand) ? get_class($operand) : gettype($operand)
            ));
        }
    }

    /**
     * Returns a new Money object that represents
     * the multiplied value by the given factor.
     *
     * @param float|int|string $multiplier
     *
     * @return Money
     */
    public function multiply($multiplier)
    {
        $this->assertOperand($multiplier);

        $product = $this->getCalculator()->multiply($this->amount, $multiplier);

        return $this->newInstance($product);
    }

    /**
     * Returns a new Money object that represents
     * the divided value by the given factor.
     *
     * @param float|int|string $divisor
     *
     * @return Money
     */
    public function divide($divisor)
    {
        $this->assertOperand($divisor);

        if ($this->getCalculator()->compare((string) $divisor, '0') === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        $quotient = $this->getCalculator()->divide($this->amount, $divisor);

        return $this->newInstance($quotient);
    }

    /**
     * Allocate the money according to a list of ratios.
     *
     * @param array $ratios
     *
     * @return Money[]
     */
    public function allocate(array $ratios)
    {
        if (count($ratios) === 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, ratios cannot be an empty array');
        }

        $remainder = $this->amount;
        $results = [];
        $total = array_sum($ratios);

        if ($total <= 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, sum of ratios must be greater than zero');
        }

        foreach ($ratios as $ratio) {
            if ($ratio < 0) {
                throw new \InvalidArgumentException('Cannot allocate to none, ratio must be zero or positive');
            }

            $share = $this->getCalculator()->share($this->amount, $ratio, $total);
            $results[] = $this->newInstance($share);
            $remainder = $this->getCalculator()->subtract($remainder, $share);
        }

        for ($i = 0; $this->getCalculator()->compare($remainder, 0) === 1; ++$i) {
            $results[$i]->amount = (string) $this->getCalculator()->add($results[$i]->amount, 1);
            $remainder = $this->getCalculator()->subtract($remainder, 1);
        }

        return $results;
    }

    /**
     * Allocate the money among N targets.
     *
     * @param int $n
     *
     * @return Money[]
     *
     * @throws \InvalidArgumentException If number of targets is not an integer
     */
    public function allocateTo($n)
    {
        if (!is_int($n)) {
            throw new \InvalidArgumentException('Number of targets must be an integer');
        }

        if ($n <= 0) {
            throw new \InvalidArgumentException('Cannot allocate to none, target must be greater than zero');
        }

        return $this->allocate(array_fill(0, $n, 1));
    }

    /**
     * @return Money
     */
    public function absolute()
    {
        return $this->newInstance($this->getCalculator()->absolute($this->amount));
    }

    /**
     * Checks if the value represented by this object is zero.
     *
     * @return bool
     */
    public function isZero()
    {
        return $this->getCalculator()->compare($this->amount, 0) === 0;
    }

    /**
     * Checks if the value represented by this object is positive.
     *
     * @return bool
     */
    public function isPositive()
    {
        return $this->getCalculator()->compare($this->amount, 0) === 1;
    }

    /**
     * Checks if the value represented by this object is negative.
     *
     * @return bool
     */
    public function isNegative()
    {
        return $this->getCalculator()->compare($this->amount, 0) === -1;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    /**
     * @param string $calculator
     */
    public static function registerCalculator($calculator)
    {
        if (is_a($calculator, Calculator::class, true) === false) {
            throw new \InvalidArgumentException('Calculator must implement '.Calculator::class);
        }

        array_unshift(self::$calculators, $calculator);
    }

    /**
     * @return Calculator
     *
     * @throws \RuntimeException If cannot find calculator for money calculations
     */
    private static function initializeCalculator()
    {
        $calculators = self::$calculators;

        foreach ($calculators as $calculator) {
            /** @var Calculator $calculator */
            if ($calculator::supported()) {
                return new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations');
    }

    /**
     * @return Calculator
     */
    private function getCalculator()
    {
        if (null === self::$calculator) {
            self::$calculator = self::initializeCalculator();
        }

        return self::$calculator;
    }
}
