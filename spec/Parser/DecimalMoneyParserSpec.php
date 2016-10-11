<?php

namespace spec\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DecimalMoneyParserSpec extends ObjectBehavior
{
    function let(Currencies $currencies)
    {
        $this->beConstructedWith($currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Parser\DecimalMoneyParser');
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(Currencies $currencies)
    {
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $this->parse('1.00', 'EUR')->shouldEqualsMoney(new Money(100, new Currency('EUR')));
    }

    function it_does_not_parse_when_there_is_no_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€ 100');
    }

    function it_does_not_parse_when_money_includes_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€ 100', 'EUR');
    }

    function it_does_not_parse_when_money_is_not_a_valid_decimal()
    {
        $this->shouldThrow(ParserException::class)->duringParse('INVALID', 'EUR');
    }

    function it_does_not_parse_a_boolean()
    {
        $this->shouldThrow(ParserException::class)->duringParse(true);
    }

    /**
     * @return array
     */
    public function getMatchers()
    {
        return [
            'equalsMoney' => function (Money $subject, Money $value) {
                return $subject->equals($value);
            },
        ];
    }
}
