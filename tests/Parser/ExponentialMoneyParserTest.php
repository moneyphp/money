<?php

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Parser\ExponentialMoneyParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

final class ExponentialMoneyParserTest extends TestCase
{
    /**
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function it_parses_money(string $decimal, string $currency, int $subunit, int $result)
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $parser = new ExponentialMoneyParser($currencies->reveal());

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

        $parser = new ExponentialMoneyParser($currencies->reveal());

        $parser->parse($input, new Currency('USD'))->getAmount();
    }

    public function formattedMoneyExamples()
    {
        return [
            ['2.8865798640254e+15', 'USD', 2, 288657986402540000],
            ['2.8865798640254e-15', 'USD', 2, 0],
            ['0.8865798640254e+15', 'USD', 2, 88657986402540000],
            ['2.8865798640254e+15', 'JPY', 0, 2886579864025400],
            ['2.8865798640254e-15', 'JPY', 0, 0],
            ['0.8865798640254e+15', 'JPY', 0, 886579864025400],
            ['-2.8865798640254e+15', 'USD', 2, -288657986402540000],
            ['-2.8865798640254e-15', 'USD', 2, 0],
            ['-0.8865798640254e+15', 'USD', 2, -88657986402540000],
        ];
    }

    public static function invalidMoneyExamples()
    {
        return [
            ['INVALID'],
            ['2.00'],
            ['2'],
            ['0.02'],
            ['.'],
        ];
    }
}
