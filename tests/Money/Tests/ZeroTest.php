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
    public function operationsWithZeroMoney()
    {
        $this->assertEquals(
            Money::EUR(5),
            Money::EUR(5)->add(Money::zero())
        );

        $this->assertEquals(
            Money::EUR(5),
            Money::EUR(5)->subtract(Money::zero())
        );

        $this->assertEquals(
            Money::EUR(0),
            Money::EUR(5)->multiply(Money::zero())
        );

        $this->assertEquals(
            Money::EUR(0),
            Money::EUR(0)->divide(Money::EUR(5))
        );

    }

    /** @test */
    public function divisionByZero()
    {
        $this->setExpectedException('\Money\DivisionByZeroException');
        Money::EUR(5)->divide(Money::zero());
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
