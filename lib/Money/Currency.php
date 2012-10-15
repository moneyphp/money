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

use Money\Currencies;

class Currency
{
    /** @var string */
    private $name;

    /** @var string */
    private $symbol;

    /** @var string */
    private $decimalSeparator;

    /** @var string */
    private $thousandSeparator;

    const EUR = 'EUR';
    const USD = 'USD';
    const GBP = 'GBP';
    const JPY = 'JPY';
    const BRL = 'BRL';

    public function __construct($name)
    {
        if (! Currencies::exist($name)) {
            throw new UnknownCurrencyException($name);
        }

        $this->name = $name;
        $this->symbol = Currencies::getSymbol($name);
        $this->decimalSeparator = Currencies::getDecimalSeparator($name);
        $this->thousandSeparator = Currencies::getThousandSeparator($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    /**
     * @return string
     */
    public function getThousandSeparator()
    {
        return $this->thousandSeparator;
    }

    /**
     * @return bool
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
