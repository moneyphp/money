<?php

declare(strict_types=1);

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Parser\IntlLocalizedDecimalParser;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/** @covers \Money\Parser\IntlLocalizedDecimalParser */
final class IntlLocalizedDecimalParserTest extends TestCase
{
    /**
     * @phpstan-param non-empty-string $string
     * @phpstan-param non-empty-string $locale
     *
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function itParsesMoney(string $string, int $units, string $locale): void
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);

        $currencies = $this->createMock(Currencies::class);

        $currencies->method('subunitFor')
            ->with(self::callback(static fn (Currency $givenCurrency): bool => $givenCurrency->getCode() === 'USD'))
            ->willReturn(2);

        $currencyCode = 'USD';
        $currency     = new Currency($currencyCode);

        $parser = new IntlLocalizedDecimalParser($formatter, $currencies);
        self::assertEquals($units, $parser->parse($string, $currency)->getAmount());
    }

    /**
     * @test
     */
    public function itCannotConvertStringToUnits(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);

        $currency = new Currency('USD');
        $parser   = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());

        $this->expectException(ParserException::class);
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', $currency);
    }

    /**
     * @test
     */
    public function itWorksWithAllKindsOfLocales(): void
    {
        $formatter = new NumberFormatter('en_CA', NumberFormatter::DECIMAL);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money  = $parser->parse('1000.00', new Currency('CAD'));

        self::assertTrue(Money::CAD(100000)->equals($money));
    }

    /**
     * @test
     */
    public function itAcceptsAForcedCurrency(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);

        $currency = new Currency('CAD');
        $parser   = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money    = $parser->parse('1000.00', $currency);

        self::assertSame('100000', $money->getAmount());
        self::assertSame('CAD', $money->getCurrency()->getCode());
    }

    /**
     * @test
     */
    public function itSupportsFractionDigits(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money  = $parser->parse('1000.005', new Currency('USD'));

        self::assertSame('100001', $money->getAmount());
    }

    /**
     * @test
     */
    public function it_does_not_support_invalid_decimal(): void
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());

        $this->expectException(ParserException::class);

        $parser->parse('1000,005', new Currency('USD'));
    }

    /**
     * @phpstan-return non-empty-list<array{
     *     non-empty-string,
     *     int,
     *     non-empty-string
     * }>
     */
    public static function formattedMoneyExamples(): array
    {
        return [
            ['1000.50', 100050, 'en_US'],
            ['0001000.50', 100050, 'en_US'],
            ['1000.00', 100000, 'en_US'],
            ['1000.0', 100000, 'en_US'],
            ['1000.00', 100000, 'en_US'],
            ['1,000.50', 100050, 'en_US'],
            ['1,000.00', 100000, 'en_US'],
            ['1,000.0', 100000, 'en_US'],
            ['1,000.00', 100000, 'en_US'],
            ['0.01', 1, 'en_US'],
            ['0.00', 0, 'en_US'],
            ['1', 100, 'en_US'],
            ['-1000', -100000, 'en_US'],
            ['-0001000', -100000, 'en_US'],
            ['-1000.0', -100000, 'en_US'],
            ['-1000.00', -100000, 'en_US'],
            ['-1,000', -100000, 'en_US'],
            ['-1,000.0', -100000, 'en_US'],
            ['-1,000.00', -100000, 'en_US'],
            ['-0.01', -1, 'en_US'],
            ['-1', -100, 'en_US'],
            ['1000', 100000, 'en_US'],
            ['1000.0', 100000, 'en_US'],
            ['1000.00', 100000, 'en_US'],
            ['0.01', 1, 'en_US'],
            ['1', 100, 'en_US'],
            ['.99', 99, 'en_US'],
            ['-.99', -99, 'en_US'],
            ['99.', 9900, 'en_US'],
            ['1000,50', 100050, 'el_GR'],
            ['1000,00', 100000, 'el_GR'],
            ['1000,0', 100000, 'el_GR'],
            ['1000,00', 100000, 'el_GR'],
            ['1.000,50', 100050, 'el_GR'],
            ['1.000,00', 100000, 'el_GR'],
            ['1.000,0', 100000, 'el_GR'],
            ['1.000,00', 100000, 'el_GR'],
            ['-1000,50', -100050, 'el_GR'],
            ['-1000,00', -100000, 'el_GR'],
            ['-1000,0', -100000, 'el_GR'],
            ['-1000,00', -100000, 'el_GR'],
            ['-1.000,50', -100050, 'el_GR'],
            ['-1.000,00', -100000, 'el_GR'],
            ['-1.000,0', -100000, 'el_GR'],
            ['-1.000,00', -100000, 'el_GR'],
        ];
    }
}
