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

use Money\Currency;
use Money\IntlMoneyFormatter;
use Money\Money;
use PHPUnit_Framework_TestCase;

class MoneyFormatterTest extends PHPUnit_Framework_TestCase
{

    public function testRoundMoney () {
        $money = new Money(100, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 1,00', $formatter->format($money));
    }

    public function testLessThanOneHundred () {
        $money = new Money(41, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 0,41', $formatter->format($money));
    }

    public function testLessThanTen () {
        $money = new Money(5, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 0,05', $formatter->format($money));
    }

    public function testDifferentDigits () {
        $formatter = new IntlMoneyFormatter('nl_NL', 3);
        $this->assertEquals('€ 0,005', $formatter->format(new Money(5, new Currency('EUR'))));
        $this->assertEquals('€ 0,035', $formatter->format(new Money(35, new Currency('EUR'))));
        $this->assertEquals('€ 0,135', $formatter->format(new Money(135, new Currency('EUR'))));
        $this->assertEquals('€ 6,135', $formatter->format(new Money(6135, new Currency('EUR'))));
    }

}
