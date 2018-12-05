<?php

namespace Tests\Money\Currencies;

use Money\Currencies\CurrencyList;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

final class CurrencyListTest extends TestCase
{
    private static $correctCurrencies = [
        'MY1' => 2,
        'MY2' => 0,
        'MY3' => 1,
    ];

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_has_currencies($currency)
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function it_provides_subunit($currency)
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->assertInternalType('int', $currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown()
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->expectException(UnknownCurrencyException::class);

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function it_is_iterable()
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $iterator = $currencies->getIterator();

        $this->assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @dataProvider invalidInstantiation
     * @test
     */
    public function it_does_not_initialize_if_array_is_invalid(array $currencies)
    {
        $this->expectException(\InvalidArgumentException::class);

        new CurrencyList($currencies);
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
            [[1 => 2]],
            [['' => 2]],
            [['OWO' => []]],
            [['OWO' => null]],
            [['OWO' => '']],
            [['OWO' => -2]],
            [['OWO' => 2.1]],
        ];
    }
}
