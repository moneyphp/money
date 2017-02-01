<?php

namespace Tests\Money;

use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\HistoricalConverter;
use Money\HistoricalExchange;
use Money\Money;
use Prophecy\Prophecy\ObjectProphecy;

final class HistoricalConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider convertExamples
     * @test
     */
    public function it_converts_to_a_different_currency(
        $baseCurrencyCode,
        $counterCurrencyCode,
        $subunitBase,
        $subunitCounter,
        $ratio,
        $amount,
        $expectedAmount,
        \DateTimeInterface $date
    ) {
        $baseCurrency = new Currency($baseCurrencyCode);
        $counterCurrency = new Currency($counterCurrencyCode);
        $pair = new CurrencyPair($baseCurrency, $counterCurrency, $ratio);

        /** @var Currencies|ObjectProphecy $currencies */
        $currencies = $this->prophesize(Currencies::class);

        /** @var HistoricalExchange|ObjectProphecy $exchange */
        $exchange = $this->prophesize(HistoricalExchange::class);

        $converter = new HistoricalConverter($currencies->reveal(), $exchange->reveal());

        $currencies->subunitFor($baseCurrency)->willReturn($subunitBase);
        $currencies->subunitFor($counterCurrency)->willReturn($subunitCounter);

        $exchange->historical($baseCurrency, $counterCurrency, $date)->willReturn($pair);

        $money = $converter->convert(
            new Money($amount, new Currency($baseCurrencyCode)),
            $counterCurrency,
            $date
        );

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expectedAmount, $money->getAmount());
        $this->assertEquals($counterCurrencyCode, $money->getCurrency()->getCode());
    }

    public function convertExamples()
    {
        return [
            ['USD', 'JPY', 2, 0, 101, 100, 101, new \DateTime()],
            ['JPY', 'USD', 0, 2, 0.0099, 1000, 990, new \DateTime()],
            ['USD', 'EUR', 2, 2, 0.89, 100, 89, new \DateTime()],
            ['EUR', 'USD', 2, 2, 1.12, 100, 112, new \DateTime()],
        ];
    }
}
