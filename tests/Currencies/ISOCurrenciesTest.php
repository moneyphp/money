<?php

namespace Tests\Money\Currencies;

use function array_keys;
use function array_map;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

final class ISOCurrenciesTest extends TestCase
{
    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itHasIsoCurrencies($currency)
    {
        $currencies = new ISOCurrencies();

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesSubunit($currency)
    {
        $currencies = new ISOCurrencies();

        $this->assertIsInt($currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingSubunitAndCurrencyIsUnknown()
    {
        $this->expectException(UnknownCurrencyException::class);

        $currencies = new ISOCurrencies();

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesNumericCode($currency)
    {
        $currencies = new ISOCurrencies();

        $this->assertIsInt($currencies->numericCodeFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingNumericCodeAndCurrencyIsUnknown()
    {
        $this->expectException(UnknownCurrencyException::class);

        $currencies = new ISOCurrencies();

        $currencies->numericCodeFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function itIsIterable()
    {
        $currencies = new ISOCurrencies();

        $iterator = $currencies->getIterator();

        $this->assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    public function currencyCodeExamples()
    {
        $currencies = require __DIR__.'/../../resources/currency.php';
        $currencies = array_keys($currencies);

        return array_map(function ($currency) {
            return [$currency];
        }, $currencies);
    }
}
