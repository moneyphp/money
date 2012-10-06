<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Tests;

use PHPUnit_Framework_TestCase;
use Money\Currency;

class CurrencyTest extends PHPUnit_Framework_TestCase
{

    const DOT = '.';
    const COMMA = ',';

    private $euro1;
    private $euro2;
    private $usd1;
    private $usd2;
    private $brl1;
    private $gbp1;

    public function setUp()
    {
        $this->euro1 = new Currency('EUR');
        $this->euro2 = new Currency('EUR');
        $this->usd1 = new Currency('USD');
        $this->usd2 = new Currency('USD');
        $this->gbp1 = new Currency('GBP');
        $this->brl1 = new Currency('BRL');
    }

    public function testDifferentInstancesAreEqual()
    {
        $this->assertTrue(
            $this->euro1->equals($this->euro2)
        );
        $this->assertTrue(
            $this->usd1->equals($this->usd2)
        );
    }

    public function testDifferentCurrenciesAreNotEqual()
    {
        $this->assertFalse(
            $this->euro1->equals($this->usd1)
        );
    }

    /**
     * @test
     * @expectedException Money\UnknownCurrencyException
     */
    public function testCantInstantiateUnknownCurrency()
    {
        new Currency('unknonw');
    }

    public function testDecimalSeparator()
    {
        $this->assertEquals(self::DOT, $this->euro1->getDecimalSeparator());
        $this->assertEquals(self::DOT, $this->usd1->getDecimalSeparator());
        $this->assertEquals(self::DOT, $this->gbp1->getDecimalSeparator());
        // JPY ?
        $this->assertEquals(self::COMMA, $this->brl1->getDecimalSeparator());
    }

    public function testThousandSeparator()
    {
        $this->assertEquals(self::COMMA, $this->euro1->getThousandSeparator());
        $this->assertEquals(self::COMMA, $this->usd1->getThousandSeparator());
        $this->assertEquals(self::COMMA, $this->gbp1->getThousandSeparator());
        // JPY ?
        $this->assertEquals(self::DOT, $this->brl1->getThousandSeparator());
    }
}
