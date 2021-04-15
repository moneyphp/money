<?php

declare(strict_types=1);

namespace spec\Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;
use PhpSpec\ObjectBehavior;

final class AggregateMoneyParserSpec extends ObjectBehavior
{
    public function let(MoneyParser $moneyParser): void
    {
        $this->beConstructedWith([$moneyParser]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AggregateMoneyParser::class);
    }

    public function it_is_a_money_parser(): void
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(MoneyParser $moneyParser): void
    {
        $money = new Money(10000, new Currency('EUR'));

        $moneyParser->parse('€ 100', null)->willReturn($money);

        $this->parse('€ 100', null)->shouldReturn($money);
    }

    public function it_throws_an_exception_when_money_cannot_be_parsed(MoneyParser $moneyParser): void
    {
        $moneyParser->parse('INVALID', null)->willThrow(ParserException::class);

        $this->shouldThrow(ParserException::class)->duringParse('INVALID', null);
    }
}
