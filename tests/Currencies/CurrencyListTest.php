<?php

namespace Tests\Money\Currencies;

use function array_keys;
use function array_map;
use InvalidArgumentException;
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
    public function itHasCurrencies($currency)
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->assertTrue($currencies->contains(new Currency($currency)));
    }

    /**
     * @dataProvider currencyCodeExamples
     * @test
     */
    public function itProvidesSubunit($currency)
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->assertIsInt($currencies->subunitFor(new Currency($currency)));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenProvidingSubunitAndCurrencyIsUnknown()
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $this->expectException(UnknownCurrencyException::class);

        $currencies->subunitFor(new Currency('XXXXXX'));
    }

    /**
     * @test
     */
    public function itIsIterable()
    {
        $currencies = new CurrencyList(self::$correctCurrencies);

        $iterator = $currencies->getIterator();

        $this->assertContainsOnlyInstancesOf(Currency::class, $iterator);
    }

    /**
     * @dataProvider invalidInstantiation
     * @test
     */
    public function itDoesNotInitializeIfArrayIsInvalid(array $currencies)
    {
        $this->expectException(InvalidArgumentException::class);

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
