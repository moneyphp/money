<?php

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

final class DecimalMoneyParserTest extends TestCase
{
    /**
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function it_parses_money($decimal, $currency, $subunit, $result)
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $this->assertEquals($result, $parser->parse($decimal, new Currency($currency))->getAmount());
    }

    /**
     * @dataProvider invalidMoneyExamples
     * @test
     */
    public function it_throws_an_exception_upon_invalid_inputs($input)
    {
        $this->expectException(ParserException::class);

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $parser->parse($input, new Currency('USD'))->getAmount();
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a Money\Currency instance instead.
     * @test
     */
    public function it_accepts_only_a_currency_object()
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $parser->parse('1.0', 'USD')->getAmount();
    }

    public function formattedMoneyExamples()
    {
        return [
            ['1000.50', 'USD', 2, 100050],
            ['1000.00', 'USD', 2, 100000],
            ['1000.0', 'USD', 2, 100000],
            ['1000', 'USD', 2, 100000],
            ['0.01', 'USD', 2, 1],
            ['0.00', 'USD', 2, 0],
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
            ['', 'USD', 2, 0],
            ['.99', 'USD', 2, 99],
            ['99.', 'USD', 2, 9900],
            ['-9.999', 'USD', 2, -1000],
            ['9.999', 'USD', 2, 1000],
            ['9.99', 'USD', 2, 999],
            ['-9.99', 'USD', 2, -999],
        ];
    }

    public static function invalidMoneyExamples()
    {
        return [
            ['INVALID'],
            ['.'],
        ];
    }
}
