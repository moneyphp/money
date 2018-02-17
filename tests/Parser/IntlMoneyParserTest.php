<?php

namespace Tests\Money\Parser;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Money\Parser\IntlMoneyParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

final class IntlMoneyParserTest extends TestCase
{
    /**
     * @dataProvider formattedMoneyExamples
     *
     * @test
     */
    public function it_parses_money($string, $units)
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencies = $this->prophesize(Currencies::class);

        $currencies->subunitFor(Argument::allOf(
            Argument::type(Currency::class),
            Argument::which('getCode', 'USD')
        ))->willReturn(2);

        $currencyCode = 'USD';
        $currency = new Currency($currencyCode);

        $parser = new IntlMoneyParser($formatter, $currencies->reveal());
        $this->assertEquals($units, $parser->parse($string, $currency)->getAmount());
    }

    /**
     * @test
     */
    public function it_cannot_convert_string_to_units()
    {
        $this->expectException(ParserException::class);

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencyCode = 'USD';
        $currency = new Currency($currencyCode);
        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $parser->parse('THIS_IS_NOT_CONVERTABLE_TO_UNIT', $currency);
    }

    /**
     * @test
     */
    public function it_works_with_all_kinds_of_locales()
    {
        $formatter = new \NumberFormatter('en_CA', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money = $parser->parse('$1000.00');

        $this->assertEquals(Money::CAD(100000), $money);
    }

    /**
     * @test
     */
    public function it_accepts_a_forced_currency()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $currencyCode = 'CAD';
        $currency = new Currency($currencyCode);
        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money = $parser->parse('$1000.00', $currency);

        $this->assertEquals('100000', $money->getAmount());
        $this->assertEquals('CAD', $money->getCurrency()->getCode());
    }

    /**
     * @test
     */
    public function it_supports_fraction_digits()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money = $parser->parse('$1000.005');

        $this->assertEquals('100001', $money->getAmount());
    }

    /**
     * TODO: investigate why this test fails with segmentation fault.
     *
     * @group segmentation
     * @test
     */
    public function it_supports_fraction_digits_with_different_style_and_pattern()
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $money = $parser->parse('$1000.005');

        $this->assertEquals('100001', $money->getAmount());
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing a currency as string is deprecated since 3.1 and will be removed in 4.0. Please pass a Money\Currency instance instead.
     * @test
     */
    public function it_accepts_only_a_currency_object()
    {
        $formatter = new \NumberFormatter('en_CA', \NumberFormatter::CURRENCY);
        $formatter->setPattern('¤#,##0.00;-¤#,##0.00');

        $parser = new IntlMoneyParser($formatter, new ISOCurrencies());
        $parser->parse('$1000.00', 'EUR');
    }

    public function formattedMoneyExamples()
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
        ];
    }
}
