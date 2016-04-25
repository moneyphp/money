<?php

namespace spec\Money\Parser;

use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;

class StringToUnitsParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Parser\StringToUnitsParser');
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    /**
     * @dataProvider moneyExamples
     */
    public function it_parses_money($string, $units)
    {
        $money = $this->parse($string, 'EUR');

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($units);
        $money->getCurrency()->getCode()->shouldReturn('EUR');
    }

    public function moneyExamples()
    {
        return [
            ['1000', 100000],
            ['1000.0', 100000],
            ['1000.00', 100000],
            ['0.01', 1],
            ['1', 100],
            ['-1000', -100000],
            ['-1000.0', -100000],
            ['-1000.00', -100000],
            ['-0.01', -1],
            ['-1', -100],
            ['+1000', 100000],
            ['+1000.0', 100000],
            ['+1000.00', 100000],
            ['+0.01', 1],
            ['+1', 100],
            ['.99', 99],
            ['-.99', -99],
            ['0', '0'],
            ['-0', '0'],
        ];
    }

    function it_throws_an_exception_when_money_cannot_be_parsed()
    {
        $this->shouldThrow(ParserException::class)->duringParse('INVALID', 'EUR');
    }

    function it_throws_an_exception_when_there_is_no_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€ 100');
    }

    function it_does_not_parse_a_boolean()
    {
        $this->shouldThrow(ParserException::class)->duringParse(true);
    }
}
