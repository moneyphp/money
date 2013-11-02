<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money;

use Money\Contracts\MoneyInterface;
use Money\Contracts\CurrencyInterface;

class Money implements MoneyInterface
{
    /**
     * @var int
     */
    private $amount;

    /** @var \Money\Currency */
    private $currency;

    /**
     * @{inheritDoc}
     */
    public function __construct($amount, CurrencyInterface $currency)
    {
        if (!is_int($amount)) {
            throw new InvalidArgumentException("The first parameter of Money must be an integer. It's the amount, expressed in the smallest units of currency (eg cents)");
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Convenience factory method for a Money object
     * @example $fiveDollar = Money::USD(500);
     * @param string $method
     * @param array $arguments
     * @return \Money\Money
     */
    public static function __callStatic($method, $arguments)
    {
        return new Money($arguments[0], new Currency($method));
    }

    /**
     * @{inheritDoc}
     */
    public function isSameCurrency(MoneyInterface $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertSameCurrency(MoneyInterface $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Different currencies');
        }
    }

    /**
     * @{inheritDoc}
     */
    public function equals(MoneyInterface $other)
    {
        return
            $this->isSameCurrency($other)
            && $this->amount == $other->amount;
    }

    /**
     * @{inheritDoc}
     */
    public function compare(MoneyInterface $other)
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
     * @{inheritDoc}
     */
    public function greaterThan(MoneyInterface $other)
    {
        return 1 == $this->compare($other);
    }

    /**
     * @{inheritDoc}
     */
    public function lessThan(MoneyInterface $other)
    {
        return -1 == $this->compare($other);
    }

    /**
     * @{inheritDoc}
     */
    public function getUnits()
    {
        return $this->amount;
    }

    /**
     * @{inheritDoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @{inheritDoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @{inheritDoc}
     */
    public function add(MoneyInterface $addend)
    {
        $this->assertSameCurrency($addend);

        return new self($this->amount + $addend->amount, $this->currency);
    }

    /**
     * @{inheritDoc}
     */
    public function subtract(MoneyInterface $subtrahend)
    {
        $this->assertSameCurrency($subtrahend);

        return new self($this->amount - $subtrahend->amount, $this->currency);
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand should be an integer or a float');
        }
    }

    /**
     * @throws \Money\InvalidArgumentException
     */
    private function assertRoundingMode($rounding_mode)
    {
        if (!in_array($rounding_mode, array(self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP))) {
            throw new InvalidArgumentException('Rounding mode should be Money::ROUND_HALF_DOWN | Money::ROUND_HALF_EVEN | Money::ROUND_HALF_ODD | Money::ROUND_HALF_UP');
        }
    }

    /**
     * @{inheritDoc}
     */
    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);
        $this->assertRoundingMode($rounding_mode);

        $product = (int) round($this->amount * $multiplier, 0, $rounding_mode);

        return new Money($product, $this->currency);
    }

    /**
     * @{inheritDoc}
     */
    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($divisor);
        $this->assertRoundingMode($rounding_mode);

        $quotient = (int) round($this->amount / $divisor, 0, $rounding_mode);

        return new Money($quotient, $this->currency);
    }

    /**
     * @{inheritDoc}
     */
    public function allocate(array $ratios = array())
    {
        $remainder = $this->amount;
        $results = array();
        $total = array_sum($ratios);

        foreach ($ratios as $ratio) {
            $share = (int) floor($this->amount * $ratio / $total);
            $results[] = new Money($share, $this->currency);
            $remainder -= $share;
        }
        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->amount++;
            $remainder--;
        }

        return $results;
    }

    /**
     * @{inheritDoc}
     */
    public function isZero()
    {
        return $this->amount === 0;
    }

    /**
     * @{inheritDoc}
     */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /**
     * @{inheritDoc}
     */
    public function isNegative()
    {
        return $this->amount < 0;
    }

    /**
     * @{inheritDoc}
     */
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
