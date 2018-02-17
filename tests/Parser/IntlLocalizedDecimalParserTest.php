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
    public function it_parses_money($string, $units)
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

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
        $this->expectException(ParserException::class);

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        $currencyCode = 'USD';
        $currency = new Currency($currencyCode);
        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
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

        $this->assertEquals(Money::CAD(100000), $money);
    }

    /**
     * @test
     */
    public function it_accepts_a_forced_currency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);

        $currencyCode = 'CAD';
        $currency = new Currency($currencyCode);
        $parser = new IntlLocalizedDecimalParser($formatter, new ISOCurrencies());
        $money = $parser->parse('1000.00', $currency);

        $this->assertEquals('100000', $money->getAmount());
        $this->assertEquals('CAD', $money->getCurrency()->getCode());
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

        $this->assertEquals('100001', $money->getAmount());
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
            ['1000.50', 100050],
            ['1000.00', 100000],
            ['1000.0', 100000],
            ['1000.00', 100000],
            ['0.01', 1],
            ['0.00', 0],
            ['1', 100],
            ['-1000', -100000],
            ['-1000.0', -100000],
            ['-1000.00', -100000],
            ['-0.01', -1],
            ['-1', -100],
            ['1000', 100000],
            ['1000.0', 100000],
            ['1000.00', 100000],
            ['0.01', 1],
            ['1', 100],
            ['.99', 99],
            ['-.99', -99],
            ['99.', 9900],
        ];
    }
}
