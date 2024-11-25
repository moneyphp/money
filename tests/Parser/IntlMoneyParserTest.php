<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Parser\IntlMoneyParser */
final class IntlMoneyParserTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $string
     *
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function itParsesMoney(string $string, int $units): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $givenCurrency->getCode() === 'USD'))
            ->willReturn(2);

        $currencyCode = 'USD';
        $currency     = new Currency($currencyCode);

        $parser = new IntlMoneyParser($formatter, $currencies);
        self::assertEquals($units, $parser->parse($string, $currency)->getAmount());
    }

    /**
     * @test
     */
    public function itCannotConvertStringToUnits(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencyCode = 'USD';
        $currency     = new Currency($currencyCode);
        $parser       = new IntlMoneyParser($formatter, new ISOCurrencies());

        $this->expectException(ParserException::class);
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', $currency);
    }

    /**
     * @test
     */
    public function itWorksWithAllKindsOfLocales(): void
    {
        $formatter = new NumberFormatter('en_CA', NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money  = $parser->parse('$1000.00');

        self::assertTrue(Money::CAD(100000)->equals($money));
    }

    /**
     * @test
     */
    public function itAcceptsAForcedCurrency(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencyCode = 'CAD';
        $currency     = new Currency($currencyCode);
        $parser       = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money        = $parser->parse('$1000.00', $currency);

        self::assertEquals('100000', $money->getAmount());
        self::assertEquals('CAD', $money->getCurrency()->getCode());
    }

    /**
     * @test
     */
    public function itSupportsFractionDigits(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money  = $parser->parse('$1000.005');

        self::assertEquals('100001', $money->getAmount());
    }

    /**
     * TODO: investigate why this test fails with segmentation fault.
     *
     * @group segmentation
     * @test
     */
    public function itSupportsFractionDigitsWithDifferentStyleAndPattern(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money  = $parser->parse('$1000.005');

        self::assertEquals('100001', $money->getAmount());
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     non-empty-string,
     *     int
     * }>
     */
    public static function formattedMoneyExamples(): array
    {
        return [
            ['$1000.50', 100050],
            ['$1000.00', 100000],
            ['$1000.0', 100000],
            ['$1000.00', 100000],
            ['$0.01', 1],
            ['$0.00', 0],
            ['$1', 100],
            ['-$1000', -100000],
            ['-$1000.0', -100000],
            ['-$1000.00', -100000],
            ['-$0.01', -1],
            ['-$1', -100],
            ['$1000', 100000],
            ['$1000.0', 100000],
            ['$1000.00', 100000],
            ['$0.01', 1],
            ['$1', 100],
            ['$.99', 99],
            ['-$.99', -99],
            ['$99.', 9900],
            ['$00099.', 9900],
            ['-$00099.', -9900],
            ['$000', 0],
        ];
    }
}
