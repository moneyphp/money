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
use Money\Currencies;

class CurrenciesTest extends PHPUnit_Framework_TestCase
{
    public function testNumberOfCurrencies()
    {
        $this->assertEquals(5, count(Currencies::all()));
    }

    public function testExist()
    {
        $this->assertTrue(Currencies::exist('EUR'));
        $this->assertTrue(Currencies::exist('USD'));
        $this->assertTrue(Currencies::exist('GBP'));
        $this->assertTrue(Currencies::exist('JPY'));
        $this->assertTrue(Currencies::exist('BRL'));

        $this->assertFalse(Currencies::exist('XYZ'));
    }

    public function testGetSymbol()
    {
        $this->assertEquals('€', Currencies::getSymbol('EUR'));
        $this->assertEquals('$', Currencies::getSymbol('USD'));
        $this->assertEquals('$', Currencies::getSymbol('GBP'));
        $this->assertEquals('¥', Currencies::getSymbol('JPY'));
        $this->assertEquals('R$', Currencies::getSymbol('BRL'));
    }

    public function testGetDecimalSeparator()
    {
        $this->assertEquals('.', Currencies::getDecimalSeparator('EUR'));
        $this->assertEquals('.', Currencies::getDecimalSeparator('USD'));
        $this->assertEquals('.', Currencies::getDecimalSeparator('GBP'));
        $this->assertEquals('', Currencies::getDecimalSeparator('JPY'));
        $this->assertEquals(',', Currencies::getDecimalSeparator('BRL'));
    }

    public function testGetThousandSeparator()
    {
        $this->assertEquals(',', Currencies::getThousandSeparator('EUR'));
        $this->assertEquals(',', Currencies::getThousandSeparator('USD'));
        $this->assertEquals(',', Currencies::getThousandSeparator('GBP'));
        $this->assertEquals('', Currencies::getThousandSeparator('JPY'));
        $this->assertEquals('.', Currencies::getThousandSeparator('BRL'));
    }
}