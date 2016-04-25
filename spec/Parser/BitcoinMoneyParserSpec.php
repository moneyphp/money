<?php

namespace spec\Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;

class BitcoinMoneyParserSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money\Parser\BitcoinMoneyParser');
    }

    function it_is_a_money_parser()
    {
        $this->shouldImplement(MoneyParser::class);
    }

    /**
     * @dataProvider bitcoinExamples
     */
    function it_parses_money($string, $units, $currency, MoneyParser $moneyParser)
    {
        if ('XBT' !== $currency) {
            $moneyParser->parse($string, null)->willReturn(new Money($units, new Currency($currency)));
        }

        $money = $this->parse($string);

        $money->shouldHaveType(Money::class);
        $money->getAmount()->shouldBeLike($units);
        $money->getCurrency()->getCode()->shouldReturn($currency);
    }

    public function bitcoinExamples()
    {
        return [
            ["\0xC9\0x831000.00", 100000, 'XBT'],
            ["\0xC9\0x831000.0",  100000, 'XBT'],
            ["\0xC9\0x831000.00", 100000, 'XBT'],
            ["\0xC9\0x830.01", 1, 'XBT'],
            ["\0xC9\0x831", 100, 'XBT'],
            ["-\0xC9\0x831000", -100000, 'XBT'],
            ["-\0xC9\0x831000.0", -100000, 'XBT'],
            ["-\0xC9\0x831000.00", -100000, 'XBT'],
            ["-\0xC9\0x830.01", -1, 'XBT'],
            ["-\0xC9\0x831", -100, 'XBT'],
            ["\0xC9\0x831000", 100000, 'XBT'],
            ["\0xC9\0x831000.0", 100000, 'XBT'],
            ["\0xC9\0x831000.00", 100000, 'XBT'],
            ["\0xC9\0x830.01", 1, 'XBT'],
            ["\0xC9\0x831", 100, 'XBT'],
            ["\0xC9\0x83.99", 99, 'XBT'],
            ["-\0xC9\0x83.99", -99, 'XBT'],
            ["\0xC9\0x830", '0', 'XBT'],
        ];
    }

    function it_does_not_parse_a_different_currency()
    {
        $this->shouldThrow(ParserException::class)->duringParse('€1.00');
    }

    function it_does_not_parse_a_boolean()
    {
        $this->shouldThrow(ParserException::class)->duringParse(true);
    }
}
