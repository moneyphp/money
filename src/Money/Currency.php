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
     * ISO 4217 currency code
     *
     * @var string
     */
    private $name;

    /**
     * Known currencies
     *
     * @var array
     */
    private static $currencies;

    /**
     * @param string $name
     *
     * @throws UnknownCurrencyException If currency is not known
     */
    public function __construct($name)
    {
        if(!isset(static::$currencies)) {
           static::$currencies = require __DIR__.'/currencies.php';
        }

        if (!array_key_exists($name, static::$currencies)) {
            throw new UnknownCurrencyException($name);
        }
        $this->name = $name;
    }

    /**
     * Returns the ISO 4217 currency code
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        return $this->name === $other->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
