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

class Currency
{
    /** @var string */
    private $name;

    /** @var string */
    private $decimalSeparator;

    /** @var string */
    private $thousandSeparator;

    const EUR = 'EUR';
    const USD = 'USD';
    const GBP = 'GBP';
    const JPY = 'JPY';
    const BRL = 'BRL';

    private  $decimalSeparators = array(
        'EUR' => '.',
        'USD' => '.',
        'GBP' => '.',
        // JPY?
        'BRL' => ',',
    );

    private  $thousandSeparators = array(
        'EUR' => ',',
        'USD' => ',',
        'GBP' => ',',
        // JPY?
        'BRL' => '.',
    );

    public function __construct($name)
    {
        if (!defined("self::$name")) {
            throw new UnknownCurrencyException($name);
        }
        $this->name = $name;
        $this->setDecimalSeparator();
        $this->setThousandSeparator();
    }

    /**
     * @return string
     */
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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

    private function setDecimalSeparator()
    {
        $this->decimalSeparator = $this->decimalSeparators[$this->name];
    }

    public function getDecimalSeparator()
    {
        return $this->decimalSeparator;
    }

    private function setThousandSeparator()
    {
        $this->thousandSeparator = $this->thousandSeparators[$this->name];
    }

    public function getThousandSeparator()
    {
        return $this->thousandSeparator;
    }
}
