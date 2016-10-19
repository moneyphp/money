<?php

namespace Tests\Money\Currencies;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

final class ISOCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_has_iso_currencies($currency)
    {
        $currencies = new ISOCurrencies();

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_provides_subunit($currency)
    {
        $currencies = new ISOCurrencies();

        $this->assertInternalType('int', $currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown()
    {
        $this->setExpectedException(UnknownCurrencyException::class);

        $currencies = new ISOCurrencies();

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function it_is_iterable()
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
