<?php

namespace Tests\Money;

use Money\Calculator\BcMathCalculator;
use Money\Calculator\GmpCalculator;
use Money\Calculator\PhpCalculator;
use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class MoneyFactoryTest extends TestCase
{
    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_using_factories(Currency $currency)
    {
        $code = $currency->getCode();
        $money = Money::{$code}(20);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency), $money);
    }

    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_using_factories_with_bcmath_calculator(Currency $currency)
    {
        $code = $currency->getCode();
        $money = Money::{$code}(20, new BcMathCalculator());

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency, new BcMathCalculator()), $money);
    }

    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_using_factories_with_gmp_calculator(Currency $currency)
    {
        $code = $currency->getCode();
        $money = Money::{$code}(20, new GmpCalculator());

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency, new GmpCalculator()), $money);
    }

    /**
     * @dataProvider currencyExamples
     * @test
     */
    public function it_creates_money_using_factories_with_php_calculator(Currency $currency)
    {
        $code = $currency->getCode();
        $money = Money::{$code}(20, new PhpCalculator());

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(new Money(20, $currency, new PhpCalculator()), $money);
    }

    public function currencyExamples()
    {
        $currencies = new AggregateCurrencies([
            new ISOCurrencies(),
            new BitcoinCurrencies(),
        ]);

        $examples = [];

        foreach ($currencies as $currency) {
            $examples[] = [$currency];
        }

        return $examples;
    }
}
