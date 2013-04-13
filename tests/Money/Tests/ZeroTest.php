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

use Money\Money;

final class ZeroTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function zeroMoneysAreEqualIndependentOfCurrency()
    {
        $this->assertTrue(
            Money::EUR(0)->equals(
                Money::USD(0)
            )
        );

        $this->assertEquals(
            Money::zero(),
            Money::zero()
        );
    }

    /** @test */
    public function zeroMoneyCanBeInstantiatedWithoutCurrency()
    {
        $this->assertEquals(
            new Money(0),
            Money::zero()
        );
    }

    /** @test */
    public function nonZeroMoneyCanNotBeInstantiatedWithoutCurrency()
    {
        $this->setExpectedException('\Money\InvalidArgumentException');
        new Money(5);
    }

    /** @test */
    public function operationsWithZeroMoney()
    {
        $this->assertEquals(
            Money::zero(),
            Money::zero()->add(Money::zero())
        );
        $this->assertEquals(
            Money::EUR(5),
            Money::EUR(5)->add(Money::zero())
        );

        $this->assertEquals(
            Money::USD(5),
            Money::zero()->add(Money::USD(5))
        );

        $this->assertEquals(
            Money::EUR(5),
            Money::EUR(5)->subtract(Money::zero())
        );

        $this->assertEquals(
            Money::EUR(0),
            Money::EUR(5)->multiply(0)
        );

        $this->assertEquals(
            Money::EUR(0),
            Money::EUR(0)->divide(5)
        );

    }

    /** @test */
    public function divisionByZero()
    {
        $this->setExpectedException('\Money\DivisionByZeroException');
        Money::EUR(5)->divide(0);
    }


    /** @test */
    public function flyweightPatternForZeroMoney()
    {
        $this->assertSame(
            Money::zero(),
            Money::zero(),
            "Creating zero objects should always return the same instance"
        );
    }
}
