<?php

namespace Tests\Money;

use Money\Currency;
use Money\IntlMoneyFormatter;
use Money\Money;

class MoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testRoundMoney()
    {
        $money = new Money(100, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 1,00', $formatter->format($money));
    }

    public function testLessThanOneHundred()
    {
        $money = new Money(41, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 0,41', $formatter->format($money));
    }

    public function testLessThanTen()
    {
        $money = new Money(5, new Currency('EUR'));
        $formatter = new IntlMoneyFormatter('nl_NL', 2);
        $this->assertEquals('€ 0,05', $formatter->format($money));
    }

    public function testDifferentDigits()
    {
        $formatter = new IntlMoneyFormatter('nl_NL', 3);
        $this->assertEquals('€ 0,005', $formatter->format(new Money(5, new Currency('EUR'))));
        $this->assertEquals('€ 0,035', $formatter->format(new Money(35, new Currency('EUR'))));
        $this->assertEquals('€ 0,135', $formatter->format(new Money(135, new Currency('EUR'))));
        $this->assertEquals('€ 6,135', $formatter->format(new Money(6135, new Currency('EUR'))));
    }
}
