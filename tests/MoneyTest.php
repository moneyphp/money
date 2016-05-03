<?php

namespace Tests\Money;

use Money\Currency;
use Money\Money;

final class MoneyTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryMethods()
    {
        $this->assertEquals(
            Money::EUR(25),
            Money::EUR(10)->add(Money::EUR(15))
        );

        $this->assertEquals(
            Money::USD(25),
            Money::USD(10)->add(Money::USD(15))
        );
    }

    public function testJsonEncoding()
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );
    }

    public function testMaxInit()
    {
        $one = new Money(1, new Currency('EUR'));

        $this->assertInstanceOf('Money\\Money', new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertInstanceOf('Money\\Money', (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        $this->assertInstanceOf('Money\\Money', (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
    }
}
