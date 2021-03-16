<?php

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Parser\IntlLocalizedDecimalParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

final class IntlLocalizedDecimalParserTest extends TestCase
{
    /**
     * @dataProvider formattedMoneyExamples
     * @test
     */
    public function it_parses_money($string, $units, $locale)
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $currencyCode = 'USD';
        $currency = new Currency($currencyCode);

        $parser = new IntlLocalizedDecimalParser($formatter, $currencies->reveal());
        $this->assertEquals($units, $parser->parse($string, $currency)->getAmount());
    }

    /**
     * @test
     */
    public function it_cannot_convert_string_to_units()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        $currency = new Currency('USD');
        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());

        $this->expectException(ParserException::class);
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', $currency);
    }

    /**
     * @test
     */
    public function it_works_with_all_kinds_of_locales()
    {
        $formatter = new \NumberFormatter('en_CA', \NumberFormatter::DECIMAL);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money = $parser->parse('1000.00', new Currency('CAD'));

        $this->assertTrue(Money::CAD(100000)->equals($money));
    }

    /**
     * @test
     */
    public function it_accepts_a_forced_currency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        $currency = new Currency('CAD');
        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money = $parser->parse('1000.00', $currency);

        $this->assertSame('100000', $money->getAmount());
        $this->assertSame('CAD', $money->getCurrency()->getCode());
    }

    /**
     * @test
     */
    public function it_supports_fraction_digits()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money = $parser->parse('1000.005', new Currency('USD'));

        $this->assertSame('100001', $money->getAmount());
    }

    public function it_does_not_support_invalid_decimal()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money = $parser->parse('1000,005', new Currency('USD'));

        $this->assertSame('100001', $money->getAmount());
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a Money\Currency instance instead.
     * @test
     */
    public function it_accepts_only_a_currency_object()
    {
        $formatter = new \NumberFormatter('en_CA', \NumberFormatter::DECIMAL);

        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $parser->parse('1000.00', 'EUR');
    }

    public function formattedMoneyExamples()
    {
        return [
            ['1000.50', 100050, 'en_US'],
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
