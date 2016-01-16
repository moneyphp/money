<?php

namespace Money;

/**
 * @coversDefaultClass Money\ISOCurrencies
 * @uses Money\Currency
 */
final class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
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
