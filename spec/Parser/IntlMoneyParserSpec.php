<?php

namespace spec\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\IntlMoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class IntlMoneyParserSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IntlMoneyParser::class);
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    function it_parses_money(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $currencyString = null;

        $numberFormatter->parseCurrency('€1.00', $currencyString)->willReturn(1);
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $currency = new Currency('EUR');
        $money = $this->parse('€1.00', $currency);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldReturn('100');
        $money->getCurrency()->getCode()->shouldReturn('EUR');
    }

    function it_throws_an_exception_when_money_cannot_be_parsed(\NumberFormatter $numberFormatter)
    {
        $currencyString = null;

        $numberFormatter->parseCurrency('INVALID', $currencyString)->willReturn(false);
        $numberFormatter->getErrorMessage()->willReturn('Some message');

        $currency = new Currency('EUR');

        $this->shouldThrow(ParserException::class)->duringParse('INVALID', $currency);
    }
}
