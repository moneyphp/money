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
 * @coversDefaultClass Money\CurrencyPair
 * @uses Money\Currency
 * @uses Money\Money
 * @uses Money\CurrencyPair
 */
class CurrencyPairTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $eur = new Currency('EUR');
        $usd = new Currency('USD');
        $ratio = 1.0;

        $pair = new CurrencyPair($eur, $usd, $ratio);

        $this->assertSame($eur, $pair->getBaseCurrency());
        $this->assertSame($usd, $pair->getCounterCurrency());
        $this->assertEquals($ratio, $pair->getConversionRatio());
    }

    /**
     * @covers ::__construct
     * @dataProvider provideNonNumericRatio
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Conversion ratio must be numeric
     */
    public function testConstructorWithNonNumericRatio($nonNumericRatio)
    {
        new CurrencyPair(new Currency('EUR'), new Currency('USD'), $nonNumericRatio);
    }

    /**
     * @covers ::getRatio
     * @covers ::getConversionRatio
     */
    public function testGetRatio()
    {
        $ratio = 1.2500;
        $pair  = new CurrencyPair(new Currency('EUR'), new Currency('USD'), $ratio);

        $this->assertEquals($ratio, $pair->getRatio());
        $this->assertEquals($ratio, $pair->getConversionRatio());
    }

    /**
     * @covers ::getBaseCurrency
     */
    public function testGetBaseCurrency()
    {
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $this->assertEquals(new Currency('EUR'), $pair->getBaseCurrency());
    }

    /**
     * @covers ::getCounterCurrency
     */
    public function testGetCounterCurrency()
    {
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $this->assertEquals(new Currency('USD'), $pair->getCounterCurrency());
    }

    /**
     * @covers ::convert
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The Money has the wrong currency
     */
    public function testConvertWithInvalidCurrency()
    {
        $money = new Money(100, new Currency('JPY'));
        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);

        $pair->convert($money);
    }

    /**
     * @covers ::convert
     */
    public function testConvertsEurToUsdAndBack()
    {
        $eur = Money::EUR(100);

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $usd = $pair->convert($eur);
        $this->assertEquals(Money::USD(125), $usd);

        $pair = new CurrencyPair(new Currency('USD'), new Currency('EUR'), 0.8000);
        $eur = $pair->convert($usd);
        $this->assertEquals(Money::EUR(100), $eur);
    }

    /**
     * @covers ::convert
     */
    public function testConvertsEurToUsdWithModes()
    {
        $eur = Money::EUR(10);

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $usd = $pair->convert($eur);
        $this->assertEquals(Money::USD(13), $usd);

        $pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $usd = $pair->convert($eur, PHP_ROUND_HALF_DOWN);
        $this->assertEquals(Money::USD(12), $usd);
    }

    /**
     * @covers ::equals
     * @dataProvider provideEqualityComparisonPairs
     */
    public function testEqualityComparisons($pair1, $pair2, $equal)
    {
        $this->assertSame($equal, $pair1->equals($pair2));
    }

    /**
     * @covers ::createFromIso
     */
    public function testParsesIso()
    {
        $pair = CurrencyPair::createFromIso('EUR/USD 1.2500');
        $expected = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
        $this->assertEquals($expected, $pair);
    }

    /**
     * @covers ::createFromIso
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can't create currency pair from ISO string '1.2500', format of string is invalid
     */
    public function testParsesIsoWithException()
    {
        CurrencyPair::createFromIso('1.2500');
    }

    public function provideEqualityComparisonPairs()
    {
        $usd = new Currency('USD');
        $eur = new Currency('EUR');
        $gbp = new Currency('GBP');

        return array(
            'Base Mismatch EUR != GBP' => array(
                new CurrencyPair($eur, $usd, 1.2500),
                new CurrencyPair($gbp, $usd, 1.2500),
                false
            ),
            'Counter Mismatch USD != GBP' => array(
                new CurrencyPair($eur, $usd, 1.2500),
                new CurrencyPair($eur, $gbp, 1.2500),
                false
            ),
            'Ratio Mismatch 1.2500 != 1.5000' => array(
                new CurrencyPair($eur, $usd, 1.2500),
                new CurrencyPair($eur, $usd, 1.5000),
                false
            ),
            'Full Equality EUR/USD 1.2500' => array(
                new CurrencyPair($eur, $usd, 1.2500),
                new CurrencyPair($eur, $usd, 1.2500),
                true
            ),
        );
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
