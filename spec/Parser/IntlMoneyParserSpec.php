<?php

declare(strict_types=1);

namespace spec\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class IntlMoneyParserSpec extends ObjectBehavior
{
    public function let(NumberFormatter $numberFormatter, Currencies $currencies): void
    {
        $this->beConstructedWith($numberFormatter, $currencies);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(IntlMoneyParser::class);
    }

    public function it_is_a_money_parser(): void
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(NumberFormatter $numberFormatter, Currencies $currencies): void
    {
        $currencyString = '';

        $numberFormatter->parseCurrency('€1.00', $currencyString)->willReturn(1);
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $currency = new Currency('EUR');
        $money    = $this->parse('€1.00', $currency);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldReturn('100');
        $money->getCurrency()->getCode()->shouldReturn('EUR');
    }

    public function it_throws_an_exception_when_money_cannot_be_parsed(NumberFormatter $numberFormatter): void
    {
        $currencyString = '';

        $numberFormatter->parseCurrency('INVALID', $currencyString)->willReturn(false);
        $numberFormatter->getErrorMessage()->willReturn('Some message');

        $currency = new Currency('EUR');

        $this->shouldThrow(ParserException::class)->duringParse('INVALID', $currency);
    }
}
