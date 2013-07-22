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

use Symfony\Component\Intl\Intl;

class Currency
{
    /** @var string */
    private $code;

    /**
     * @param string $code
     * @throws UnknownCurrencyException
     */
    public function __construct($code)
    {
        if (null === Intl::getCurrencyBundle()->getCurrencyName($code)) {
            throw new UnknownCurrencyException($code);
        }
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName() {
        return Intl::getCurrencyBundle()->getCurrencyName($this->code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSymbol() {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->code);
    }

    /**
     * @param \Money\Currency $other
     * @return bool
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
        return $this->getName();
    }
}
