<?php

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use Prophecy\Argument;

final class DecimalMoneyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFormattedMoney
     */
    public function testDecimalParser($decimal, $currency, $subunit, $result)
    {
        $currenciesProphecy = $this->prophesize(Currencies::class);
        $currenciesProphecy->contains(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn(true);
        $currenciesProphecy->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);
        $currencies = $currenciesProphecy->reveal();

        $parser = new DecimalMoneyParser($currencies);
        $this->assertEquals($result, $parser->parse($decimal, $currency)->getAmount());
    }

    public static function provideFormattedMoney()
    {
        return [
            ['1000.50', 'USD', 2, 100050],
            ['1000.00', 'USD', 2, 100000],
            ['1000.0', 'USD', 2, 100000],
            ['1000', 'USD', 2, 100000],
            ['0.01', 'USD', 2, 1],
            ['1', 'USD', 2, 100],
            ['-1000.50', 'USD', 2, -100050],
            ['-1000.00', 'USD', 2, -100000],
            ['-1000.0', 'USD', 2, -100000],
            ['-1000', 'USD', 2, -100000],
            ['-0.01', 'USD', 2, -1],
            ['-1', 'USD', 2, -100],
            ['1000.501', 'USD', 3, 1000501],
            ['1000.001', 'USD', 3, 1000001],
            ['1000.50', 'USD', 3, 1000500],
            ['1000.00', 'USD', 3, 1000000],
            ['1000.0', 'USD', 3, 1000000],
            ['1000', 'USD', 3, 1000000],
            ['0.001', 'USD', 3, 1],
            ['0.01', 'USD', 3, 10],
            ['1', 'USD', 3, 1000],
            ['-1000.501', 'USD', 3, -1000501],
            ['-1000.001', 'USD', 3, -1000001],
            ['-1000.50', 'USD', 3, -1000500],
            ['-1000.00', 'USD', 3, -1000000],
            ['-1000.0', 'USD', 3, -1000000],
            ['-1000', 'USD', 3, -1000000],
            ['-0.001', 'USD', 3, -1],
            ['-0.01', 'USD', 3, -10],
            ['-1', 'USD', 3, -1000],
            ['1000.50', 'JPY', 0, 1001],
            ['1000.00', 'JPY', 0, 1000],
            ['1000.0', 'JPY', 0, 1000],
            ['1000', 'JPY', 0, 1000],
            ['0.01', 'JPY', 0, 0],
            ['1', 'JPY', 0, 1],
            ['-1000.50', 'JPY', 0, -1001],
            ['-1000.00', 'JPY', 0, -1000],
            ['-1000.0', 'JPY', 0, -1000],
            ['-1000', 'JPY', 0, -1000],
            ['-0.01', 'JPY', 0, -0],
            ['-1', 'JPY', 0, -1],
        ];
    }
}
