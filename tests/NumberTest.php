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

use Money\Number;
use PHPUnit_Framework_TestCase;

class NumberTest extends PHPUnit_Framework_TestCase
{

    public function testDecimal()
    {
        $number = new Number(10);
        $this->assertFalse($number->isDecimal());

        $number = new Number(10.5);
        $this->assertTrue($number->isDecimal());

        $number = new Number('10.5');
        $this->assertTrue($number->isDecimal());

        $number = new Number(PHP_INT_MAX);
        $this->assertFalse($number->isDecimal());
    }

    public function testHalf()
    {
        $number = new Number(10);
        $this->assertFalse($number->isHalf());

        $number = new Number(10.5);
        $this->assertTrue($number->isHalf());

        $number = new Number('10.5');
        $this->assertTrue($number->isHalf());

        $number = new Number('10.500');
        $this->assertTrue($number->isHalf());

        $number = new Number(PHP_INT_MAX);
        $this->assertFalse($number->isHalf());
    }

    public function testCurrentEven()
    {
        $number = new Number(10);
        $this->assertTrue($number->isCurrentEven());

        $number = new Number(3);
        $this->assertFalse($number->isCurrentEven());

        $number = new Number(10.5);
        $this->assertTrue($number->isCurrentEven());

        $number = new Number('10.5');
        $this->assertTrue($number->isCurrentEven());

        $number = new Number('10.500');
        $this->assertTrue($number->isCurrentEven());

        $number = new Number(3.5);
        $this->assertFalse($number->isCurrentEven());

        $number = new Number('3.5');
        $this->assertFalse($number->isCurrentEven());

        $number = new Number('3.500');
        $this->assertFalse($number->isCurrentEven());
    }

}
