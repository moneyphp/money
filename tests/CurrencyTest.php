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
 * @coversDefaultClass Money\Currency
 * @uses Money\Currency
 * @uses Money\Money
 * @uses Money\CurrencyPair
 */
final class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->euro1 = new Currency('EUR');
        $this->euro2 = new Currency('EUR');
        $this->usd1 = new Currency('USD');
        $this->usd2 = new Currency('USD');
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $currency = new Currency('EUR');

        $this->assertEquals('EUR', $currency->getCode());
    }

    /**
     * @covers ::getCode
     * @covers ::getName
     * @covers ::__toString
     */
    public function testCode()
    {
        $this->assertEquals('EUR', $this->euro1->getCode());
        $this->assertEquals('EUR', $this->euro1->getName());
        $this->assertEquals('EUR', (string) $this->euro1);
    }

    /**
     * @covers ::equals
     */
    public function testDifferentInstancesAreEqual()
    {
        $this->assertTrue($this->euro1->equals($this->euro2));
        $this->assertTrue($this->usd1->equals($this->usd2));

        $anotherEur = $this->getMock('Money\CurrencyInterface');
        $anotherEur
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('EUR'))
        ;

        $this->assertTrue($this->euro1->equals($anotherEur));
    }

    /**
     * @covers ::equals
     */
    public function testDifferentCurrenciesAreNotEqual()
    {
        $this->assertFalse($this->euro1->equals($this->usd1));
    }

    public function testJsonEncoding()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }
}
