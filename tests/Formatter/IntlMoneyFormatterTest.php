<?php

namespace Tests\Money\Formatter;

use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class IntlMoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testRoundMoney()
    {
        $money = new Money(100, new Currency('USD'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('$1.00', $moneyFormatter->format($money));
    }

    public function testLessThanOneHundred()
    {
        $money = new Money(41, new Currency('USD'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('$0.41', $moneyFormatter->format($money));
    }

    public function testLessThanTen()
    {
        $money = new Money(5, new Currency('USD'));

        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('$0.05', $moneyFormatter->format($money));
    }

    public function testDifferentDigits()
    {
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);

        $this->assertEquals('$0.005', $moneyFormatter->format(new Money(5, new Currency('USD'))));
        $this->assertEquals('$0.035', $moneyFormatter->format(new Money(35, new Currency('USD'))));
        $this->assertEquals('$0.135', $moneyFormatter->format(new Money(135, new Currency('USD'))));
        $this->assertEquals('$6.135', $moneyFormatter->format(new Money(6135, new Currency('USD'))));
        $this->assertEquals('-$6.135', $moneyFormatter->format(new Money(-6135, new Currency('USD'))));
    }

    public function testDifferentLocaleAndDifferentCurrency()
    {
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('€0.05', $moneyFormatter->format(new Money(5, new Currency('EUR'))));
        $this->assertEquals('€0.50', $moneyFormatter->format(new Money(50, new Currency('EUR'))));
        $this->assertEquals('€5.00', $moneyFormatter->format(new Money(500, new Currency('EUR'))));
    }

    public function testStyleDecimalAndPattern()
    {
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $numberFormatter->setPattern("¤#,##0.00;-¤#,##0.00");

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('€0.05', $moneyFormatter->format(new Money(5, new Currency('EUR'))));
        $this->assertEquals('€0.50', $moneyFormatter->format(new Money(50, new Currency('EUR'))));
        $this->assertEquals('€5.00', $moneyFormatter->format(new Money(500, new Currency('EUR'))));
    }

    public function testStyleDecimalNoPattern()
    {
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);

        $this->assertEquals('5', $moneyFormatter->format(new Money(5, new Currency('EUR'))));
        $this->assertEquals('50', $moneyFormatter->format(new Money(50, new Currency('EUR'))));
        $this->assertEquals('500', $moneyFormatter->format(new Money(500, new Currency('EUR'))));
    }

    public function testStylePercent()
    {
        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::PERCENT);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter);
        $this->assertEquals('500%', $moneyFormatter->format(new Money(5, new Currency('EUR'))));
    }
}
