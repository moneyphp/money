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

/**
 * Currency Value Object
 *
 * Holds Currency specific data
 *
 * @author Mathias Verraes
 */
class Currency
{
    /**
     * Currency code
     *
     * @var string
     */
    private $code;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        if (!is_string($code)) {
            throw new \InvalidArgumentException('Cuurency code should be string');
        }

        $this->code = $code;
    }

    /**
     * Returns the currency code
     *
     * @return string
     *
     * @deprecated Use getCode() instead
     */
    public function getName()
    {
        return $this->code;
    }

    /**
     * Returns the currency code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Checks whether this currency is the same as an other
     *
     * @param Currency $other
     *
     * @return boolean
     */
    public function equals(Currency $other)
    {
        return $this->code === $other->code;
    }

    /**
     * Checks whether this currency is available in the passed context
     *
     * @param AvailableCurrencies $currencies
     *
     * @return boolean
     */
    public function isAvailableWithin(AvailableCurrencies $currencies)
    {
        return $currencies->exists($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }
}
