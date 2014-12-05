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

abstract class MoneyFactory
{
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

}
