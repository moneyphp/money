<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Parser\DecimalMoneyParser */
final class DecimalMoneyParserTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $currency
     * @phpstan-param non-negative-int $subunit
     * @phpstan-param int $result
     *
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function itParsesMoney(string $decimal, string $currency, int $subunit, int $result): void
    {
        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $currency === $givenCurrency->getCode()))
            ->willReturn($subunit);

        $parser = new DecimalMoneyParser($currencies);

        self::assertEquals($result, $parser->parse($decimal, new Currency($currency))->getAmount());
    }

    /**
     * @phpstan-param non-empty-string $input
     *
     * @dataProvider invalidMoneyExamples
     * @test
     */
    public function itThrowsAnExceptionUponInvalidInputs($input): void
    {
        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $givenCurrency->getCode() === 'USD'))
            ->willReturn(2);

        $parser = new DecimalMoneyParser($currencies);

        $this->expectException(ParserException::class);
        $parser->parse($input, new Currency('USD'));
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     string,
     *     non-empty-string,
     *     non-negative-int,
     *     int
     * }>
     */
    public static function formattedMoneyExamples(): array
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
            ['000009.99', 'USD', 2, 999],
            ['-000009.99', 'USD', 2, -999],
            ['000', 'USD', 2, 0],
            ['003', 'USD', 2, 300],
            ['0003', 'USD', 2, 300],
        ];
    }

    /** @phpstan-return non-empty-list<array{non-empty-string}> */
    public static function invalidMoneyExamples()
    {
        return [
            ['INVALID'],
            ['.'],
        ];
    }
}
