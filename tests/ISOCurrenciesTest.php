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
 * @uses Money\ISOCurrencies
 */
class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->currencies = new ISOCurrencies;
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $currencies = new ISOCurrencies;

        $this->assertTrue($currencies->exists(new Currency('EUR')));
        $this->assertFalse($currencies->exists(new Currency('ASD')));
    }

    /**
     * @covers ::exists
     */
    public function testExists()
    {
        $this->assertTrue($this->currencies->exists(new Currency('EUR')));
        $this->assertFalse($this->currencies->exists(new Currency('ASD')));
    }
}
