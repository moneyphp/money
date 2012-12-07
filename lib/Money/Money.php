<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

class Money
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * @var int
     */
    private $units;

    /** @var Money\Currency */
    private $currency;

    /**
     * Create a Money instance
     * @param  integer                        $units    Amount, expressed in the smallest units of $currency (eg cents)
     * @param  Money\Currency                 $currency
     * @throws Money\InvalidArgumentException
     */
    public function __construct($units, Currency $currency)
    {
        if (!is_int($units)) {
            throw new InvalidArgumentException("The first parameter of Money must be an integer");
        }
        $this->units = $units; // #todo rename to amount
        $this->currency = $currency;
    }

    /**
     * Convenience factory method for a Money object
     * @example $fiveDollar = Money::USD(500);
     * @return Money
     */
    public static function __callStatic($method, $arguments)
    {
        return new Money($arguments[0], new Currency($method));
    }

    /** @return bool */
    public function isSameCurrency(Money $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * @throws Money\InvalidArgumentException
     */
    private function assertSameCurrency(Money $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Different currencies');
        }
    }

    public function equals(Money $other)
    {
        return
            $this->isSameCurrency($other)
            && $this->units == $other->units;
    }

    public function compare(Money $other)
    {
        $this->assertSameCurrency($other);
        if ($this->units < $other->units) {
            return -1;
        } elseif ($this->units == $other->units) {
            return 0;
        } else {
            return 1;
        }
    }

    public function greaterThan(Money $other)
    {
        return 1 == $this->compare($other);
    }

    public function lessThan(Money $other)
    {
        return -1 == $this->compare($other);
    }

    /**
     * @return int
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @return Money\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    public function add(Money $addend)
    {
        $this->assertSameCurrency($addend);

        return new self($this->units + $addend->units, $this->currency);
    }

    public function subtract(Money $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->units - $subtrahend->units, $this->currency);
    }

    /**
     * @throws Money\InvalidArgumentException
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand should be an integer or a float');
        }
    }

    /**
     * @throws Money\InvalidArgumentException
     */
    private function assertRoundingMode($rounding_mode)
    {
        if (!in_array($rounding_mode, array(self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP))) {
            throw new InvalidArgumentException('Rounding mode should be Money::ROUND_HALF_DOWN | Money::ROUND_HALF_EVEN | Money::ROUND_HALF_ODD | Money::ROUND_HALF_UP');
        }
    }

    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);
        $this->assertRoundingMode($rounding_mode);

        $product = (int) round($this->units * $multiplier, 0, $rounding_mode);

        return new Money($product, $this->currency);
    }

    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($divisor);
        $this->assertRoundingMode($rounding_mode);

        $quotient = (int) round($this->units / $divisor, 0, $rounding_mode);

        return new Money($quotient, $this->currency);
    }

    /**
     * Allocate the money according to a list of ratio's
     * @param array $ratios List of ratio's
     */
    public function allocate(array $ratios)
    {
        $remainder = $this->units;
        $results = array();
        $total = array_sum($ratios);

        foreach ($ratios as $ratio) {
            $share = (int) floor($this->units * $ratio / $total);
            $results[] = new Money($share, $this->currency);
            $remainder -= $share;
        }
        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->units++;
            $remainder--;
        }

        return $results;
    }

    /** @return bool */
    public function isZero()
    {
        return $this->units === 0;
    }

    /** @return bool */
    public function isPositive()
    {
        return $this->units > 0;
    }

    /** @return bool */
    public function isNegative()
    {
        return $this->units < 0;
    }

    /** @return int */
    public static function stringToUnits( $string )
    {
        //@todo extend the regular expression with grouping characters and eventually currencies
        if (!preg_match("/(-)?(\d+)([.,])?(\d)?(\d)?/", $string, $matches)) {
            throw new InvalidArgumentException("The value could not be parsed as money");
        }
        $units = $matches[1] == "-" ? "-" : "";
        $units .= $matches[2];
        $units .= isset($matches[4]) ? $matches[4] : "0";
        $units .= isset($matches[5]) ? $matches[5] : "0";

        return (int) $units;
    }
}
