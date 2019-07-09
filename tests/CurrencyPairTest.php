<?php

namespace Tests\Money;

use Money\Currency;
use Money\CurrencyPair;
use PHPUnit\Framework\TestCase;

final class CurrencyPairTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_json()
    {
        $expectedJson = '{"baseCurrency":"EUR","counterCurrency":"USD","ratio":1.25}';
        $actualJson = json_encode(new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.25));

        $this->assertEquals($expectedJson, $actualJson);
    }

    /**
     * @test
     */
    public function it_deserializes_from_var_export()
    {
        $this->assertEquals(
            new CurrencyPair(
                new Currency('TEST1'),
                new Currency('TEST2'),
                2
            ),
            CurrencyPair::__set_state([
                'baseCurrency' => new Currency('TEST1'),
                'counterCurrency' => new Currency('TEST2'),
                'conversionRatio' => 2,
            ])
        );
        $test = new CurrencyPair(new Currency('TEST1'), new Currency('TEST2'), 2);
        $this->assertEquals(
            $test,
            eval('return '.var_export($test, true).';')
        );
    }
}
