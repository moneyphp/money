<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Parser\AggregateMoneyParser */
final class AggregateMoneyParserTest extends TestCase
{
    /** @test */
    public function it_parses_money(): void
    {
        $money         = new Money(10000, new Currency('EUR'));
        $wrappedParser = $this->createMock(MoneyParser::class);

        $wrappedParser->method('parse')
            ->with('€ 100')
            ->willReturn($money);

        self::assertEquals(
            $money,
            (new AggregateMoneyParser([$wrappedParser]))
                ->parse('€ 100')
        );
    }

    /** @test */
    public function it_throws_an_exception_when_money_cannot_be_parsed(): void
    {
        $wrappedParser1 = $this->createMock(MoneyParser::class);
        $wrappedParser2 = $this->createMock(MoneyParser::class);

        $wrappedParser1->expects(self::once())
            ->method('parse')
            ->with('€ 100')
            ->willThrowException(new ParserException());

        $wrappedParser2->expects(self::once())
            ->method('parse')
            ->with('€ 100')
            ->willThrowException(new ParserException());

        $parser = new AggregateMoneyParser([$wrappedParser1, $wrappedParser2]);

        $this->expectException(ParserException::class);
        $parser->parse('€ 100');
    }

    /** @test */
    public function it_will_retrieve_parser_result_from_first_successful_parser(): void
    {
        $money          = new Money(10000, new Currency('EUR'));
        $wrappedParser1 = $this->createMock(MoneyParser::class);
        $wrappedParser2 = $this->createMock(MoneyParser::class);
        $wrappedParser3 = $this->createMock(MoneyParser::class);

        $wrappedParser1->expects(self::once())
            ->method('parse')
            ->with('€ 100')
            ->willThrowException(new ParserException());

        $wrappedParser2->expects(self::once())
            ->method('parse')
            ->with('€ 100')
            ->willReturn($money);

        $wrappedParser3->expects(self::never())
            ->method('parse');

        self::assertEquals(
            $money,
            (new AggregateMoneyParser([$wrappedParser1, $wrappedParser2, $wrappedParser3]))
                ->parse('€ 100')
        );
    }
}
