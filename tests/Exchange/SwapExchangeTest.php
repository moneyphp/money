<?php

namespace Tests\Money\Exchange;

use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange\SwapExchange;
use Prophecy\Argument;
use Swap\Model\Rate;

/**
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class SwapExchangeTest extends \PHPUnit_Framework_TestCase
{
    public function testCurrencyPairReturned()
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('USD');

        $rate = new Rate(1.0, new \DateTime());

        $swap = $this->prophesize('Swap\SwapInterface');
        $swap->quote(Argument::type('Swap\Model\CurrencyPair'))->willReturn($rate);

        $exchange = new SwapExchange($swap->reveal());

        $currencyPair = $exchange->getCurrencyPair($baseCurrency, $counterCurrency);

        $this->assertSame(
            $baseCurrency,
            $currencyPair->getBaseCurrency()
        );

        $this->assertSame(
            $counterCurrency,
            $currencyPair->getCounterCurrency()
        );

        $this->assertSame(
            1.0,
            $currencyPair->getConversionRatio()
        );
    }

    /**
     * @expectedException Money\Exception\UnresolvableCurrencyPairException
     */
    public function testInvalidCurrencyPair()
    {
        $baseCurrency = new Currency('EUR');
        $counterCurrency = new Currency('NOPE');

        $swap = $this->prophesize('Swap\SwapInterface');
        $swap->quote(Argument::type('Swap\Model\CurrencyPair'))->willThrow('Swap\Exception\Exception');

        $exchange = new SwapExchange($swap->reveal());

        $exchange->getCurrencyPair($baseCurrency, $counterCurrency);
    }
}
