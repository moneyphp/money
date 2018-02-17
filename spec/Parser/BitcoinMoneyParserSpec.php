<?php

namespace spec\Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\BitcoinMoneyParser;
use PhpSpec\ObjectBehavior;

final class BitcoinMoneyParserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BitcoinMoneyParser::class);
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    function it_parses_money()
    {
        $money = $this->parse('Ƀ1000.00');

        $money->shouldHaveType(Money::class);
        $money->getCurrency()->getCode()->shouldReturn(BitcoinCurrencies::CODE);
    }

    function it_does_not_parse_a_different_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€1.00');
    }

    function it_does_not_parse_an_invalid_value()
    {
        $this->shouldThrow(ParserException::class)->duringParse(true);
    }
}
