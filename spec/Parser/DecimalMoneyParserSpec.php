<?php

namespace spec\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class DecimalMoneyParserSpec extends ObjectBehavior
{
    function let(Currencies $currencies)
    {
        $this->beConstructedWith($currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DecimalMoneyParser::class);
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(Currencies $currencies)
    {
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $money = $this->parse('1.00', 'EUR');

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldReturn('100');
        $money->getCurrency()->getCode()->shouldReturn('EUR');
    }

    function it_throws_an_exception_when_there_is_no_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('100');
    }

    function it_throws_an_exception_when_money_includes_currency_symbol()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€ 100', 'EUR');
    }

    function it_throws_an_exception_when_money_is_not_a_valid_decimal()
    {
        $this->shouldThrow(ParserException::class)->duringParse('INVALID', 'EUR');
    }
}
