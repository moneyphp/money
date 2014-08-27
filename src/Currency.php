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
    private $code;

    /**
     * @var Currencies
     */
    private static $defaultCurrencies;

    /**
     * @param string          $code
     * @param Currencies|null $currencies
     *
     * @throws UnknownCurrencyException If currency does not exists
     */
    public function __construct($code, Currencies $currencies = null)
    {
        if ($currencies === null) {
            $currencies = static::getDefaultCurrencies();
        }

        $currencies->assertExists($code);

        $this->code = $code;
    }

    /**
     * Returns the ISO 4217 currency code
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
     * Returns the ISO 4217 currency code
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
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }

    /**
     * Returns the already loaded default currency repository or loads it
     *
     * @return Currencies
     *
     * @codeCoverageIgnore
     */
    public static function getDefaultCurrencies()
    {
        if(!isset(static::$defaultCurrencies)) {
            $currencies = require __DIR__.'/currencies.php';

            return static::$defaultCurrencies = new Currencies($currencies);
        }

        return static::$defaultCurrencies;
    }
}
