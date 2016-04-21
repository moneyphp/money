<?php

namespace Tests\Money\Currencies;

use Money\Currency;
use Money\Currencies\AggregateCurrencies;
use Prophecy\Argument;

final class AggregateCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testItContainsCurrencies()
    {
        $currenciesMock1 = $this->prophesize('Money\Currencies');
        $currenciesMock1->contains(Argument::type('Money\Currency'))->willReturn(false);
        $currenciesMock2 = $this->prophesize('Money\Currencies');
        $currenciesMock2->contains(Argument::type('Money\Currency'))->willReturn(true);

        $currencies = new AggregateCurrencies([
            $currenciesMock1->reveal(),
            $currenciesMock2->reveal(),
        ]);

        $this->assertTrue($currencies->contains(new Currency('EUR')));
    }

    public function testItDoesNotContainCurrencies()
    {
        $currenciesMock1 = $this->prophesize('Money\Currencies');
        $currenciesMock1->contains(Argument::type('Money\Currency'))->willReturn(false);
        $currenciesMock2 = $this->prophesize('Money\Currencies');
        $currenciesMock2->contains(Argument::type('Money\Currency'))->willReturn(false);

        $currencies = new AggregateCurrencies([
            $currenciesMock1->reveal(),
            $currenciesMock2->reveal(),
        ]);

        $this->assertFalse($currencies->contains(new Currency('EUR')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsAnException()
    {
        new AggregateCurrencies([
            'currencies',
        ]);
    }
}
