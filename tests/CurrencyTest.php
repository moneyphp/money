<?php

namespace Money;

use Prophecy\Argument;

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
     * @covers ::__toString
     */
    public function testCode()
    {
        $this->assertEquals('EUR', $this->euro1->getCode());
        $this->assertEquals('EUR', (string) $this->euro1);
    }

    /**
     * @covers ::equals
     */
    public function testDifferentInstancesAreEqual()
    {
        $this->assertTrue($this->euro1->equals($this->euro2));
        $this->assertTrue($this->usd1->equals($this->usd2));
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

    public function testCurrencyIsAvailableWithinCurrencies()
    {
        $currencies = $this->prophesize('\Money\Currencies');
        $currencies->contains(Argument::type('\Money\Currency'))->willReturn(true);

        $this->assertSame(true, $this->euro1->isAvailableWithin($currencies->reveal()));
    }
}
