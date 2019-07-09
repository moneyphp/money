<?php

namespace Tests\Money;

use Money\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_json()
    {
        $this->assertEquals('"USD"', json_encode(new Currency('USD')));
    }

    /**
     * @test
     */
    public function it_deserializes_from_var_export()
    {
        $this->assertEquals(
            new Currency('TEST'),
            Currency::__set_state([
                'code' => 'TEST',
            ])
        );

        $test = new Currency('TEST');
        $this->assertEquals(
            $test,
            eval('return '.var_export($test, true).';')
        );
    }
}
