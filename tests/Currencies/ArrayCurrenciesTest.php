<?php

namespace Tests\Money\Currencies;

use Money\Currencies\ArrayCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;

final class ArrayCurrenciesTest extends \PHPUnit_Framework_TestCase
{
    private static $correctCurrencies = [
        'MY1' => ['minorUnit' => 2, 'numericCode' => 1],
        'MY2' => ['minorUnit' => 0, 'numericCode' => 2],
        'MY3' => ['minorUnit' => 1, 'numericCode' => 3],
    ];

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_has_currencies($currency)
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_provides_subunit($currency)
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->assertInternalType('int', $currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown()
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->setExpectedException(UnknownCurrencyException::class);

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_provides_numeric_code($currency)
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->assertInternalType('int', $currencies->numericCodeFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_providing_numeric_code_and_currency_is_unknown()
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->setExpectedException(UnknownCurrencyException::class);

        $currencies->numericCodeFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function it_is_iterable()
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $iterator = $currencies->getIterator();

        $this->assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @test
     */
    public function it_does_not_have_blank_currency()
    {
        $currencies = new ArrayCurrencies(self::$correctCurrencies);

        $this->assertFalse($currencies->contains(new Currency('')));
    }

    /**
     * @dataProvider invalidInstantiation
     * @test
     */
    public function it_does_not_initialize_if_array_is_invalid(array $currencies)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        
        new ArrayCurrencies($currencies);
    }

    public function currencyCodeExamples()
    {
        $currencies = array_keys(self::$correctCurrencies);

        return array_map(function ($currency) {
            return [$currency];
        }, $currencies);
    }

    public function invalidInstantiation()
    {
        return [
            [['minorUnit' => 2, 'numericCode' => 1]],
            [[1 => ['minorUnit' => 2, 'numericCode' => 2]]],
            [['' => ['minorUnit' => 2, 'numericCode' => 3]]],
            [['OWO' => ['not_minorUnit' => 2, 'numericCode' => 4]]],
            [['OWO' => ['minorUnit' => 2, 'not_numericCode' => 5]]],
            [['OWO' => ['not_minorUnit' => 2, 'not_numericCode' => 6]]],
            [['OWO' => ['minorUnit' => 2]]],
            [['OWO' => ['numericCode' => 7]]],
            [['OWO' => []]],
            [['OWO' => null]],
            [['OWO' => '']],
            [['OWO' => ['minorUnit' => -2, 'numericCode' => 8]]],
            [['OWO' => ['minorUnit' => 2.1, 'numericCode' => 9]]],
            [['OWO' => ['minorUnit' => 2, 'numericCode' => -9]]],
            [['OWO' => ['minorUnit' => 2, 'numericCode' => 1.00]]],
        ];
    }
}
