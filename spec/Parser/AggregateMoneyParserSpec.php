<?php

namespace spec\Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;
use PhpSpec\ObjectBehavior;

final class AggregateMoneyParserSpec extends ObjectBehavior
{
    function let(MoneyParser $moneyParser)
    {
        $this->beConstructedWith([$moneyParser]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregateMoneyParser::class);
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    function it_parses_money(MoneyParser $moneyParser)
    {
        $money = new Money(10000, new Currency('EUR'));

        $moneyParser->parse('€ 100', null)->willReturn($money);

        $this->parse('€ 100', null)->shouldReturn($money);
    }

    function it_throws_an_exception_when_money_cannot_be_parsed(MoneyParser $moneyParser)
    {
        $moneyParser->parse('INVALID', null)->willThrow(ParserException::class);

        $this->shouldThrow(ParserException::class)->duringParse('INVALID', null);
    }
}
