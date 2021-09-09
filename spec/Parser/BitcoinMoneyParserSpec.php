<?php

declare(strict_types=1);

namespace spec\Money\Parser;

use Money\Currencies\BitcoinCurrencies;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\BitcoinMoneyParser;
use PhpSpec\ObjectBehavior;

final class BitcoinMoneyParserSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(2);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BitcoinMoneyParser::class);
    }

    public function it_is_a_money_parser(): void
    {
        $this->shouldImplement(MoneyParser::class);
    }

    public function it_parses_money(): void
    {
        $money = $this->parse('Ƀ1000.00');

        $money->shouldHaveType(Money::class);
        $money->getCurrency()->getCode()->shouldReturn(BitcoinCurrencies::CODE);
    }

    public function it_does_not_parse_a_different_currency(): void
    {
        $this->shouldThrow(ParserException::class)->duringParse('€1.00');
    }

    public function it_does_not_parse_an_invalid_value(): void
    {
        $this->shouldThrow(ParserException::class)->duringParse(true);
    }
}
