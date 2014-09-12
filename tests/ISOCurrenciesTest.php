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
 * @coversDefaultClass Money\ISOCurrencies
 * @uses Money\Currency
 */
class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::contains
     */
    public function testConstructor()
    {
        $currencies = new ISOCurrencies;

        $this->assertTrue($currencies->contains(new Currency('EUR')));
        $this->assertFalse($currencies->contains(new Currency('ASD')));
    }
}
