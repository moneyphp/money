<?php

declare(strict_types=1);

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
    public function let(Currencies $currencies): void
    {
        $this->beConstructedWith($currencies);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DecimalMoneyParser::class);
    }

    public function it_is_a_money_parser(): void
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(Currencies $currencies): void
    {
        $currencies->subunitFor(Argument::type(Currency::class))->willReturn(2);

        $money = $this->parse('1.00', new Currency('EUR'));

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldReturn('100');
        $money->getCurrency()->getCode()->shouldReturn('EUR');
    }

    public function it_throws_an_exception_when_there_is_no_currency(): void
    {
        $this->shouldThrow(ParserException::class)->duringParse('100');
    }

    public function it_throws_an_exception_when_money_includes_currency_symbol(): void
    {
        $this->shouldThrow(ParserException::class)->duringParse('€ 100', new Currency('EUR'));
    }

    public function it_throws_an_exception_when_money_is_not_a_valid_decimal(): void
    {
        $this->shouldThrow(ParserException::class)->duringParse('INVALID', new Currency('EUR'));
    }
}
