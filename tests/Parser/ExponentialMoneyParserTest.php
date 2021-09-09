<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Parser\ExponentialMoneyParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class ExponentialMoneyParserTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @psalm-param non-empty-string $currency
     *
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function it_parses_money(string $decimal, string $currency, int $subunit, int $result): void
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $parser = new ExponentialMoneyParser($currencies->reveal());

        $this->assertEquals($result, $parser->parse($decimal, new Currency($currency))->getAmount());
    }

    /**
     * @dataProvider invalidMoneyExamples
     * @test
     */
    public function it_throws_an_exception_upon_invalid_inputs(string $input): void
    {
        $this->expectException(ParserException::class);

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $parser = new ExponentialMoneyParser($currencies->reveal());

        $parser->parse($input, new Currency('USD'));
    }

    /**
     * @return mixed[][]
     * @psalm-return non-empty-list<array{
     *     non-empty-string,
     *     non-empty-string,
     *     int,
     *     int
     * }>
     */
    public function formattedMoneyExamples(): array
    {
        return [
            ['2.8865798640254e+15', 'USD', 2, 288657986402540000],
            ['2.8865798640254e-15', 'USD', 2, 0],
            ['0.8865798640254e+15', 'USD', 2, 88657986402540000],
            ['2.8865798640254e+15', 'JPY', 0, 2886579864025400],
            ['2.8865798640254e-15', 'JPY', 0, 0],
            ['0.8865798640254e+15', 'JPY', 0, 886579864025400],
            ['-2.8865798640254e+15', 'USD', 2, -288657986402540000],
            ['-2.8865798640254e-15', 'USD', 2, 0],
            ['-0.8865798640254e+15', 'USD', 2, -88657986402540000],
        ];
    }

    /**
     * @return string[][]
     */
    public static function invalidMoneyExamples(): array
    {
        return [
            ['INVALID'],
            ['2.00'],
            ['2'],
            ['0.02'],
            ['.'],
        ];
    }
}
