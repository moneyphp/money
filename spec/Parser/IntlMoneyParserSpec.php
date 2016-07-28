<?php

namespace spec\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IntlMoneyParserSpec extends ObjectBehavior
{
    function let(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Parser\IntlMoneyParser');
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    function it_parses_money(\NumberFormatter $numberFormatter, Currencies $currencies)
    {
        $currency = null;
        $numberFormatter->parseCurrency('€1.00', $currency)->willReturn(1);
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $this->parse('€1.00', 'EUR')->shouldEqualsMoney(new Money(100, new Currency('EUR')));
    }

    function it_throws_an_exception_when_money_cannot_be_parsed(\NumberFormatter $numberFormatter)
    {
        $currency = null;
        $numberFormatter->parseCurrency('INVALID', $currency)->willReturn(false);
        $numberFormatter->getErrorMessage()->willReturn('Some message');

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
