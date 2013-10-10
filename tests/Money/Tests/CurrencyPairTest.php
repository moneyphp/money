<?php
/**
 * This file is part of the Money library
 *
 * Copyright (c) 2011-2013 Mathias Verraes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Money\Tests;

use PHPUnit_Framework_TestCase;
use Money\Money;
use Money\Currency;
use Money\CurrencyPair;

class CurrencyPairTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function ConvertsEurToUsdAndBack()
    {
        $eur = Money::EUR(100);

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $usd = $pair->convert($eur);
        $this->assertEquals(Money::USD(125), $usd);

        $pair = new CurrencyPair(new Currency('USD'), new Currency('EUR'), 0.8000);
        $eur = $pair->convert($usd);
        $this->assertEquals(Money::EUR(100), $eur);
    }

    /** @test */
    public function ParsesIso()
    {
        $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');
        $expected = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $this->assertEquals($expected, $pair);
    }

    /**
     * @expectedException \Money\InvalidArgumentException
     * @expectedExceptionMessage Can't create currency pair from ISO string '1.2500', format of string is invalid
     */
    public function ParsesIsoWithException()
    {
        CurrencyPair::createFromIso('1.2500');
    }

    /**
     * @expectedException \Money\InvalidArgumentException
     * @expectedExceptionMessage Ratio must be numeric
     * @dataProvider provideNonNumericRatio
     */
    public function testConstructorWithNonNumericRatio($nonNumericRatio)
    {
        new CurrencyPair(new Currency('EUR'), new Currency('USD'), $nonNumericRatio);
    }

    public function testGetRatio()
    {
        $ratio = 1.2500;
        $pair  = new CurrencyPair(new Currency('EUR'), new Currency('USD'), $ratio);

        $this->assertEquals($ratio, $pair->getRatio());
    }

    public function testGetBaseCurrency()
    {
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $this->assertEquals(new Currency('EUR'), $pair->getBaseCurrency());
    }

    public function testGetCounterCurrency()
    {
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $this->assertEquals(new Currency('USD'), $pair->getCounterCurrency());
    }

    /**
     * @expectedException \Money\InvalidArgumentException
     * @expectedExceptionMessage The Money has the wrong currency
     */
    public function testConvertWithInvalidCurrency()
    {
        $money = new Money(100, new Currency('JPY'));
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $pair->convert($money);
    }

    public function provideNonNumericRatio()
    {
        return array(
            array('NonNumericRatio'),
            array('16AlsoIncorrect'),
            array('10.00ThisIsToo')
        );
    }
}
