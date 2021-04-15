<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class DecimalMoneyParserTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @psalm-param numeric-string $decimal
     * @psalm-param non-empty-string $currency
     * @psalm-param positive-int $subunit
     * @psalm-param int $result
     *
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function itParsesMoney(string $decimal, string $currency, int $subunit, int $result): void
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', $currency)
        ))->willReturn($subunit);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $this->assertEquals($result, $parser->parse($decimal, new Currency($currency))->getAmount());
    }

    /**
     * @psalm-param non-empty-string $input
     *
     * @dataProvider invalidMoneyExamples
     * @test
     */
    public function itThrowsAnExceptionUponInvalidInputs($input): void
    {
        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $this->expectException(ParserException::class);
        $parser->parse($input, new Currency('USD'))->getAmount();
    }

    /**
     * @group legacy
     * @test
     */
    public function itAcceptsOnlyACurrencyObject(): void
    {
        self::markTestIncomplete('Deprecation to be removed before merging this patch');

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $parser = new DecimalMoneyParser($currencies->reveal());

        $this->expectDeprecationMessage('Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a Money\Currency instance instead.');

        $parser->parse('1.0', 'USD');
    }

    /**
     * @psalm-return non-empty-list<array{
     *     numeric-string,
     *     non-empty-string,
     *     positive-int,
     *     int
     * }>
     */
    public function formattedMoneyExamples(): array
    {
        return [
            ['1000.50', 'USD', 2, 100050],
            ['1000.00', 'USD', 2, 100000],
            ['1000.0', 'USD', 2, 100000],
            ['1000', 'USD', 2, 100000],
            ['0.01', 'USD', 2, 1],
            ['0.00', 'USD', 2, 0],
            ['1', 'USD', 2, 100],
            ['-1000.50', 'USD', 2, -100050],
            ['-1000.00', 'USD', 2, -100000],
            ['-1000.0', 'USD', 2, -100000],
            ['-1000', 'USD', 2, -100000],
            ['-0.01', 'USD', 2, -1],
            ['-1', 'USD', 2, -100],
            ['1000.501', 'USD', 3, 1000501],
            ['1000.001', 'USD', 3, 1000001],
            ['1000.50', 'USD', 3, 1000500],
            ['1000.00', 'USD', 3, 1000000],
            ['1000.0', 'USD', 3, 1000000],
            ['1000', 'USD', 3, 1000000],
            ['0.001', 'USD', 3, 1],
            ['0.01', 'USD', 3, 10],
            ['1', 'USD', 3, 1000],
            ['-1000.501', 'USD', 3, -1000501],
            ['-1000.001', 'USD', 3, -1000001],
            ['-1000.50', 'USD', 3, -1000500],
            ['-1000.00', 'USD', 3, -1000000],
            ['-1000.0', 'USD', 3, -1000000],
            ['-1000', 'USD', 3, -1000000],
            ['-0.001', 'USD', 3, -1],
            ['-0.01', 'USD', 3, -10],
            ['-1', 'USD', 3, -1000],
            ['1000.50', 'JPY', 0, 1001],
            ['1000.00', 'JPY', 0, 1000],
            ['1000.0', 'JPY', 0, 1000],
            ['1000', 'JPY', 0, 1000],
            ['0.01', 'JPY', 0, 0],
            ['1', 'JPY', 0, 1],
            ['-1000.50', 'JPY', 0, -1001],
            ['-1000.00', 'JPY', 0, -1000],
            ['-1000.0', 'JPY', 0, -1000],
            ['-1000', 'JPY', 0, -1000],
            ['-0.01', 'JPY', 0, -0],
            ['-1', 'JPY', 0, -1],
            ['', 'USD', 2, 0],
            ['.99', 'USD', 2, 99],
            ['99.', 'USD', 2, 9900],
            ['-9.999', 'USD', 2, -1000],
            ['9.999', 'USD', 2, 1000],
            ['9.99', 'USD', 2, 999],
            ['-9.99', 'USD', 2, -999],
        ];
    }

    /** @psalm-return non-empty-list<array{non-empty-string}> */
    public static function invalidMoneyExamples()
    {
        return [
            ['INVALID'],
            ['.'],
        ];
    }
}
