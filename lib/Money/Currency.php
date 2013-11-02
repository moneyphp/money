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

use Money\Contracts\CurrencyInterface;

class Currency implements CurrencyInterface
{
    /** @var string */
    private $name;

    /** @var array */
    private static $currencies;

    /**
     * @param string $name
     * @throws UnknownCurrencyException
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
     * @{inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @{inheritDoc}
     */
    public function equals(CurrencyInterface $other)
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